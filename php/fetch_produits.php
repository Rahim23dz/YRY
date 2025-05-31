<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db.php';

$role = $_SESSION['role'] ?? 'guest';

$vehicule   = $_POST['vehicule'] ?? 'all';
$wilaya     = $_POST['wilaya'] ?? 'all';
$sous_type  = $_POST['sous_type'] ?? '';
$prix_min   = $_POST['prix_min'] ?? '';
$prix_max   = $_POST['prix_max'] ?? '';

$params = [];
$conditions = [];

if ($sous_type !== '') {
    $conditions[] = "p.id_sous_type = ?";
    $params[] = (int)$sous_type;
}

if ($prix_min !== '') {
    $conditions[] = "p.prix >= ?";
    $params[] = (float)$prix_min;
}

if ($prix_max !== '') {
    $conditions[] = "p.prix <= ?";
    $params[] = (float)$prix_max;
}

if ($vehicule !== 'all') {
    $conditions[] = "p.id_vehicule = ?";
    $params[] = (int)$vehicule;
}

if ($wilaya !== 'all') {
    $conditions[] = "v.id_wilaya = ?";
    $params[] = (int)$wilaya;
}

// استعلام SQL
$sql = "
    SELECT 
        p.*, 
        v.phone, 
        v.username, 
        w.nom_wilaya,
        p.etat
    FROM 
        products p
    LEFT JOIN 
        vendeurs v ON p.id_vendeur = v.id_vendeur
    LEFT JOIN 
        wilaya w ON v.id_wilaya = w.id_wilaya
";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => '❌ Failed to prepare statement: ' . $conn->error]);
    exit;
}

// ربط المتغيرات
if (!empty($params)) {
    $types = implode('', array_map(function($param) {
        return is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
    }, $params));

    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => '❌ Execution failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// إرسال النتيجة
echo json_encode([
    'role' => $role,
    'products' => $products
]);
?>
