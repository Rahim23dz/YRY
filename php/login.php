<?php
session_start();
require_once('db.php');  // تضمين ملف الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Méthode non autorisée";
    exit;
}

$username = trim($_POST['loginUser'] ?? '');
$password = $_POST['loginPass'] ?? '';

if (empty($username) || empty($password)) {
    echo "Champs manquants";
    exit;
}

// تحقق في جدول users
$stmtUser = $conn->prepare("SELECT id_user, username, password, role FROM users WHERE username = ? LIMIT 1");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    echo strtolower($user['role']);  // تحوّل إلى حروف صغيرة
    $stmtUser->close();
    $conn->close();
    exit;
}
$stmtUser->close();

// تحقق في جدول vendeurs
$stmtVendeur = $conn->prepare("SELECT id_vendeur, username, password, status FROM vendeurs WHERE username = ? LIMIT 1");
$stmtVendeur->bind_param("s", $username);
$stmtVendeur->execute();
$resultVendeur = $stmtVendeur->get_result();
$vendeur = $resultVendeur->fetch_assoc();

if ($vendeur && password_verify($password, $vendeur['password'])) {
    $_SESSION['user_id'] = $vendeur['id_vendeur'];
    $_SESSION['username'] = $vendeur['username'];
    $_SESSION['role'] = 'vendeur';

    $status = strtolower(trim($vendeur['status']));  // تحويل الحالة للحروف الصغيرة والتخلص من الفراغات

    if ($status === 'accepter' || $status === 'accepte') {
        echo 'accepter';
    } elseif ($status === 'refuse' || $status === 'refuser') {
        echo 'refuser';
    } else {
        echo 'en_attente';
    }

    $stmtVendeur->close();
    $conn->close();
    exit;
}

$stmtVendeur->close();
$conn->close();

echo "Nom d'utilisateur ou mot de passe incorrect";
?>
