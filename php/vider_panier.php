<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM panier WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("Location: ../html/panier.php?msg=cleared");
exit();
?>
