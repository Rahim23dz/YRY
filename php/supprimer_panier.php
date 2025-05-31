<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id']) && isset($_GET['id_product'])) {
    $id_user = $_SESSION['user_id'];
    $id_product = intval($_GET['id_product']);

    $sql = "DELETE FROM panier WHERE id_user = ? AND id_product = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_user, $id_product);
    if ($stmt->execute()) {
        header("Location: ../html/panier.php"); // إعادة التوجيه إلى صفحة السلة
        exit;
    } else {
        echo "Erreur lors de la suppression.";
    }

    $stmt->close();
} else {
    echo "Paramètres manquants ou utilisateur non connecté.";
}
$conn->close();
?>
