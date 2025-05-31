<?php
session_start();
require_once 'db.php';

if (isset($_POST['id_product'], $_POST['quantite']) && isset($_SESSION['user_id'])) {
    $id_product = intval($_POST['id_product']);
    $quantite = intval($_POST['quantite']);
    $id_user = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE panier SET quantite = ? WHERE id_user = ? AND id_product = ?");
    $stmt->bind_param("iii", $quantite, $id_user, $id_product);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
?>
