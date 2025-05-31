<?php
session_start();
require_once '../php/db.php';

// التحقق من صلاحية دخول المسؤول
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../html/admin_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب بيانات المسؤول
$stmt = $conn->prepare("SELECT username, email, created_at FROM admin WHERE id_admin = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Administrateur introuvable.";
    exit();
}

$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Profil Administrateur - YRY</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin_profil.css">
</head>
<body>

<header>
    <div class="header-container">
        <h1>Profil Administrateur</h1>
        <nav>
            <a href="admin_dashboard.php">Tableau de bord</a>
            <a href="../php/logout.php">Déconnexion</a>
        </nav>
    </div>
</header>

<main>
    <div class="profile-card">
        <h2>Bienvenue, <?php echo htmlspecialchars($user['username']); ?> 👋</h2>
        <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Inscrit depuis :</strong> <?php echo date('d/m/Y à H:i', strtotime($user['created_at'])); ?></p>
    </div>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> YRY - Tous droits réservés</p>
</footer>

</body>
</html>
