<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "yry");

// التحقق من الاتصال
if ($conn->connect_error) {
    echo "Échec de la connexion à la base de données: " . $conn->connect_error;
    exit;
}

// استعلام لجلب الفئات
$query = "SELECT id_categorie, nom_categorie FROM categories";
$result = $conn->query($query);

// التحقق من وجود فئات
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// تحويل الفئات إلى تنسيق JSON وإرسالها
echo json_encode($categories);

// غلق الاتصال
$conn->close();
?>
