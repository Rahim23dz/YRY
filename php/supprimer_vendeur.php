<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_vendeur'])) {
        die("ID du vendeur manquant.");
    }

    $id_vendeur = intval($_POST['id_vendeur']);

    // هنا ممكن تضيف تحقق أو حذف بيانات مرتبطة قبل حذف البائع (مثلاً الطلبات... إلخ)

    $sql_delete = "DELETE FROM vendeurs WHERE id_vendeur = $id_vendeur";

    if ($conn->query($sql_delete) === TRUE) {
        header('Location: gerer_vendeurs.php?message=Suppression réussie');
        exit;
    } else {
        die("Erreur lors de la suppression : " . $conn->error);
    }
} else {
    die("Requête non autorisée.");
}
