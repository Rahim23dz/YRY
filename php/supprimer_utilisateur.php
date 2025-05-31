<?php
session_start();
include '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_user'])) {
    $id_user = intval($_POST['id_user']);

    // حذف بيانات السلة المرتبطة
    $sqlDeletePanier = "DELETE FROM panier WHERE id_user = ?";
    $stmt1 = $conn->prepare($sqlDeletePanier);
    $stmt1->bind_param("i", $id_user);
    $stmt1->execute();

    // حذف المستخدم
    $sqlDeleteUser = "DELETE FROM users WHERE id_user = ?";
    $stmt2 = $conn->prepare($sqlDeleteUser);
    $stmt2->bind_param("i", $id_user);

    if ($stmt2->execute()) {
        header("Location: gerer_utilisateurs.php?msg=Utilisateur supprimé avec succès");
        exit();
    } else {
        echo "Erreur lors de la suppression: " . $conn->error;
    }
} else {
    header("Location: gerer_utilisateurs.php");
    exit();
}
?>
