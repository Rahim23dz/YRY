<?php
session_start();
include('db.php');

// تأكيد تسجيل الدخول كبائع
$id_vendeur = $_SESSION['user_id'] ?? null;
if (!$id_vendeur) {
    echo "❌ خطأ: يجب تسجيل الدخول كبائع.";
    exit;
}

// جلب العنوان والولاية من جدول vendeurs
$stmtVendeur = $conn->prepare("SELECT adresse_exacte, wilaya FROM vendeurs WHERE id_vendeur = ?");
$stmtVendeur->bind_param("i", $id_vendeur);
$stmtVendeur->execute();
$resultVendeur = $stmtVendeur->get_result();
$vendeur = $resultVendeur->fetch_assoc();

if (!$vendeur) {
    echo "❌ خطأ: لم يتم العثور على بيانات البائع.";
    exit;
}

$adresse_exacte = $vendeur['adresse_exacte'];
$wilaya = $vendeur['wilaya'];

// استقبال بيانات المنتج
$nom         = trim($_POST['nom'] ?? '');
$prix        = trim($_POST['prix'] ?? '');
$description = trim($_POST['description'] ?? '');
$categorie   = trim($_POST['categorie'] ?? '');
$sous_type   = trim($_POST['sous_type'] ?? '');
$id_vehicule = intval($_POST['id_vehicule'] ?? 0);

// تحقق من القيم
if (empty($nom) || empty($prix) || empty($description) || empty($categorie) || empty($sous_type) || $id_vehicule === 0) {
    echo "❌ جميع الحقول مطلوبة.";
    exit;
}

// تحقق من وجود الفئة
$stmtCat = $conn->prepare("SELECT id_categorie FROM categories WHERE id_categorie = ?");
$stmtCat->bind_param("i", $categorie);
$stmtCat->execute();
$resCat = $stmtCat->get_result();
if ($resCat->num_rows === 0) {
    echo "❌ الفئة غير موجودة.";
    exit;
}

// معالجة رفع الصورة
$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['image']['type'], $allowed)) {
        echo "❌ امتداد الصورة غير مدعوم.";
        exit;
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_name = uniqid('prod_', true) . '.' . $ext;
    $image_path = $upload_dir . $new_name;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
        echo "❌ فشل في رفع الصورة.";
        exit;
    }
} else {
    echo "❌ يرجى اختيار صورة.";
    exit;
}

// إدخال المنتج
$stmt = $conn->prepare("INSERT INTO products 
    (nom, description, categorie, sous_type, prix, image, id_vehicule, adresse_exacte, wilaya, id_vendeur) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssissssssi", 
    $nom, $description, $categorie, $sous_type, $prix, $image_path, $id_vehicule, $adresse_exacte, $wilaya, $id_vendeur
);

if ($stmt->execute()) {
    echo "✅ تم إضافة المنتج بنجاح.";
} else {
    echo "❌ خطأ أثناء الحفظ: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
