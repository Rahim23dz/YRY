<?php
session_start();
include '../php/db.php';
header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "الطريقة غير مدعومة.";
    exit;
}

$username = trim($_POST['signupUser'] ?? '');
$email = trim($_POST['signupEmail'] ?? '');
$phone = trim($_POST['signupPhone'] ?? '');
if (!preg_match('/^(?:\+213|0)(5|6|7)\d{8}$/', $phone)) {
    echo " رقم الهاتف غير صالح. يجب أن يبدأ بـ +213 أو 0 متبوعًا برقم من 5 أو 6 أو 7 ثم 8 أرقام.";
    exit;
}
$password = $_POST['signupPass'] ?? '';
$role = $_POST['signupRole'] ?? 'client';
$adresse = trim($_POST['signupAdresse'] ?? '');
$wilaya = trim($_POST['signupWilaya'] ?? '');

// التحقق من الحقول الأساسية
if (!$username || !$email || !$phone || !$password || !$adresse || !$wilaya) {
    echo "يرجى ملء جميع الحقول المطلوبة.";
    exit;
}

// التحقق من صلاحية البريد
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "البريد الإلكتروني غير صالح.";
    exit;
}

// التحقق من تكرار اسم المستخدم أو البريد
$checkQuery = $conn->prepare("
    SELECT username FROM users WHERE username = ? OR email = ?
    UNION
    SELECT username FROM vendeurs WHERE username = ? OR email = ?
");
$checkQuery->bind_param("ssss", $username, $email, $username, $email);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();
if ($checkResult->num_rows > 0) {
    echo "اسم المستخدم أو البريد الإلكتروني مستعمل مسبقاً.";
    exit;
}
$checkQuery->close();

// التحقق من معلومات البائع
$storeName = '';
$storeAddress = '';
if ($role === 'vendeur') {
    $storeName = trim($_POST['storeName'] ?? '');
    $storeAddress = trim($_POST['storeAddress'] ?? '');
    if (!$storeName || !$storeAddress) {
        echo "يرجى إدخال اسم المتجر وعنوانه.";
        exit;
    }
}

// معالجة الصورة
$defaultImage = "default";
$savedImagePath = $defaultImage;

if (isset($_FILES['signupImage']) && $_FILES['signupImage']['error'] === 0) {
    $profileImage = $_FILES['signupImage'];
    $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 2 * 1024 * 1024;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $profileImage['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        echo "صيغة الصورة غير مدعومة.";
        exit;
    }

    if ($profileImage['size'] > $maxSize) {
        echo "حجم الصورة يتجاوز الحد المسموح (2MB).";
        exit;
    }

    $uploadDir = '../uploads/profil/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
    $uniqueName = uniqid('profile_', true) . '.' . $ext;
    $target = $uploadDir . $uniqueName;

    if (!move_uploaded_file($profileImage['tmp_name'], $target)) {
        echo "فشل في رفع الصورة.";
        exit;
    }

    $savedImagePath = $uniqueName;
}

// تشفير كلمة المرور
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($role === 'vendeur') {
    // إدخال بيانات البائع بدون العنوان التفصيلي
    $stmt = $conn->prepare("INSERT INTO vendeurs (username, email, password, phone, profile_image, store_name, store_address, id_wilaya, date_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssss", $username, $email, $hashedPassword, $phone, $savedImagePath, $storeName, $storeAddress, $wilaya);
    $stmt->execute();
    $last_id = $conn->insert_id;
    $stmt->close();

    // إضافة العنوان المفصل في جدول address مرتبط بالبائع
    $stmt2 = $conn->prepare("INSERT INTO address (vendeur_id, id_wilaya, adresse_exacte) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $last_id, $wilaya, $adresse);
    $stmt2->execute();
    $stmt2->close();

} else {
    // إدخال بيانات المستخدم (client)
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone, profile_image, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $phone, $savedImagePath, $role);
    $stmt->execute();
    $last_id = $conn->insert_id;
    $stmt->close();

    // إضافة العنوان المفصل في جدول address مرتبط بالمستخدم
    $stmt2 = $conn->prepare("INSERT INTO address (user_id, id_wilaya, adresse_exacte) VALUES (?, ?, ?)");
    $stmt2->bind_param("iss", $last_id, $wilaya, $adresse);
    $stmt2->execute();
    $stmt2->close();
}

echo "success";
exit;
?>
