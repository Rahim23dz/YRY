<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id_product'])) {
        die("ID produit manquant.");
    }

    $id_product = intval($_POST['id_product']);

    // حذف المنتج من قاعدة البيانات
    $sql_delete = "DELETE FROM products WHERE id_product=$id_product";

    if ($conn->query($sql_delete) === TRUE) {
        // إعادة توجيه للصفحة الرئيسية بعد الحذف
        header("Location: gerer_products.php");
        exit;
    } else {
        echo "Erreur lors de la suppression: " . $conn->error;
    }
} else {
    die("Méthode non autorisée.");
}
