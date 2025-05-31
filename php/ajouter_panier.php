<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    $id_user = intval($_SESSION['user_id']);
    $id_product = isset($_POST['id_product']) ? intval($_POST['id_product']) : 0;
    $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 1;

    if ($id_product > 0 && $quantite > 0) {
        $sql = "SELECT quantite FROM panier WHERE id_user = ? AND id_product = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_user, $id_product);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_quantite = $row['quantite'] + $quantite;

            $update_sql = "UPDATE panier SET quantite = ? WHERE id_user = ? AND id_product = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $new_quantite, $id_user, $id_product);
            $update_stmt->execute();
        } else {
            $insert_sql = "INSERT INTO panier (id_user, id_product, quantite) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $id_user, $id_product, $quantite);
            $insert_stmt->execute();
        }

        echo "success";
    } else {
        http_response_code(400);
        echo "Produit ou quantité invalide.";
    }
} else {
    http_response_code(401);
    echo "Vous devez être connecté.";
}
?>