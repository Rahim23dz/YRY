<?php
$servername = "localhost";
$username = "root";  // عادة ما يكون "root" في بيئة محلية
$password = "";      // كلمة مرور قاعدة البيانات إذا كانت موجودة
$dbname = "yry";     // اسم قاعدة البيانات

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!$conn) {
    die("Échec de la connexion à la base de données.");
}

?>
