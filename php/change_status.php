<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
var_dump($_SESSION['role']);
// تحقق من وجود role في الجلسة
if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'غير مسموح']);
    exit;
}

$role = $_SESSION['role'];

if (!isset($_POST['id_vendeur']) || !is_numeric($_POST['id_vendeur'])) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

$id = intval($_POST['id_vendeur']);

if (!isset($_POST['action']) || $_POST['action'] !== 'refuse') {
    echo json_encode(['success' => false, 'message' => 'Action non autorisée']);
    exit;
}

// جلب اسم ملف الـ recus قبل التحديث
$getRecus = $conn->prepare("SELECT recus FROM vendeurs WHERE id_vendeur = ?");
$getRecus->bind_param("i", $id);
$getRecus->execute();
$getResult = $getRecus->get_result();

if ($getResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Vendeur non trouvé']);
    exit;
}

$row = $getResult->fetch_assoc();
$recusFile = $row['recus'];

if (!empty($recusFile)) {
    $filePath = '../uploads/recus/' . $recusFile;
    if (file_exists($filePath)) {
        unlink($filePath); // حذف الملف
    }
}

// تحديث الحالة إلى "refuse" وتفريغ عمود recus
$sql = "UPDATE vendeurs SET status = 'refuse', recus = NULL WHERE id_vendeur = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête']);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response = ['success' => true];

        // فقط لو الدور vendeur نرسل أمر logout
       if (isset($role) && $role === 'vendeur') {
    $response['logout'] = true;
} else {
    // لا تفعل logout لأي دور آخر (admin، client، ...)
}


        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune modification']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
}

$stmt->close();
$conn->close();
exit;
?>
