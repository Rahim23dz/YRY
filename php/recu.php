<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "yry");
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// إدخال الدفع
$id_vendeur = $_POST['id_vendeur'];
$montant = $_POST['montant'];
$mode = $_POST['mode_paiement'];

// سجل الدفع في قاعدة البيانات
$sql = "INSERT INTO payments_vendeurs (id_vendeur, montant, mode_paiement, statut) 
        VALUES ('$id_vendeur', '$montant', '$mode', 'en_attente')";

$conn->query($sql);

// جلب بيانات البائع
$result = $conn->query("SELECT username FROM vendeurs WHERE id_vendeur = $id_vendeur");
$row = $result->fetch_assoc();
$vendeur_name = $row['username'];

// تحضير معلومات الإيصال
$date = date('Y-m-d H:i:s');
$recu_text = "REÇU DE PAIEMENT\n\nNom: $vendeur_name\nMontant: $montant DA\nDate: $date\nMode: $mode";

// إعداد صورة الإيصال
$width = 400;
$height = 250;
$image = imagecreatetruecolor($width, $height);

// الألوان
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, $width, $height, $white);

// كتابة النص باستعمال خط TTF
$font_path = __DIR__ . "/arial.ttf"; // لازم يكون arial.ttf في نفس المجلد
if (!file_exists($font_path)) {
    die("خط arial.ttf غير موجود!");
}
imagettftext($image, 14, 0, 20, 40, $black, $font_path, $recu_text);

// تأكد من وجود مجلد uploads/recus
$dir = "uploads/recus";
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

// حفظ الصورة
$recu_name = "recu_" . $id_vendeur . "_" . time() . ".png";
$recu_path = $dir . "/" . $recu_name;
imagepng($image, $recu_path);
imagedestroy($image);

// عرض الرابط
echo "✅ تم إنشاء إيصال الدفع:<br><img src='$recu_path' width='300'>";
?>
