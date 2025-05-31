<?php
session_start();
require_once '../php/db.php';

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        // جلب id_admin و كلمة السر من قاعدة البيانات
        $sql = "SELECT id_admin, password FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id_admin, $db_password);
            $stmt->fetch();

            // مقارنة نصية مباشرة (غير مشفرة)
            if ($password === $db_password) {
                $_SESSION['user_id'] = $id_admin;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['role'] = 'admin';

                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect";
        }

        $stmt->close();
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../css/login.css" />
</head>
<body>

<header>
    <div class="header-left">
        <h1>Connexion Admin</h1>
    </div>
</header>

<main>
    <div class="form-container">
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form" action="">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required autofocus />

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required />

            <button type="submit">Se connecter</button>
        </form>
    </div>
</main>

<footer>
    Tous droits réservés &copy; <?= date('Y') ?>
</footer>

</body>
</html>
