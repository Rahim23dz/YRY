<?php
include 'db.php';
header('Content-Type: application/json');

// ✅ التأكد من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// ✅ التحقق من ID
if (!isset($_GET['id_vendeur']) || !is_numeric($_GET['id_vendeur'])) {
    echo json_encode(['error' => 'ID vendeur invalide']);
    exit;
}

$id = intval($_GET['id_vendeur']);

// ✅ تجهيز الاستعلام
$sql = "SELECT status FROM vendeurs WHERE id_vendeur = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Erreur préparation: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

// ✅ تنفيذ الاستعلام
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Erreur exécution: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => $row['status']]);
} else {
    echo json_encode(['error' => 'Vendeur non trouvé']);
}

$stmt->close();
$conn->close();
exit;
