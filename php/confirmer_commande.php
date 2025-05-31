<?php
session_start();
include '../php/db.php';

// التحقق من أن المستخدم قد سجل دخوله
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red;'>Veuillez vous connecter pour passer une commande.</p>";
    exit;
}

$id_user = $_SESSION['user_id'];
$totalGeneral = 0;
$produits = [];

// استعلام للحصول على جميع المنتجات في السلة
$sql = "SELECT p.id_product, p.nom, p.prix, pa.quantite, (p.prix * pa.quantite) AS total
        FROM panier pa
        JOIN products p ON pa.id_product = p.id_product
        WHERE pa.id_user = ?";
$stmt = $conn->prepare($sql);

// التحقق من نجاح التحضير
if ($stmt === false) {
    die('Erreur de préparation de la requête: ' . $conn->error);
}

$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

// إضافة المنتجات إلى السلة وحساب الإجمالي
while ($row = $result->fetch_assoc()) {
    $produits[] = $row;
    $totalGeneral += $row['total'];
}

// إذا كانت السلة فارغة
if (empty($produits)) {
    echo "<p style='color:red;'>Votre panier est vide. Impossible de confirmer la commande.</p>";
    exit;
}

// إدخال الطلب في جدول الطلبات
$sql_order = "INSERT INTO commandes (id_user, total, date_commande) VALUES (?, ?, NOW())";
$stmt_order = $conn->prepare($sql_order);

// التحقق من نجاح التحضير
if ($stmt_order === false) {
    die('Erreur de préparation de la requête de commande: ' . $conn->error);
}

$stmt_order->bind_param("id", $id_user, $totalGeneral);
$stmt_order->execute();

// جلب معرف الطلب الذي تم إضافته
$order_id = $stmt_order->insert_id;

// إدخال تفاصيل المنتجات التي تم طلبها
foreach ($produits as $product) {
    $sql_details = "INSERT INTO details_commande (id_commande, id_product, quantite, total) VALUES (?, ?, ?, ?)";
    $stmt_details = $conn->prepare($sql_details);

    // التحقق من نجاح التحضير
    if ($stmt_details === false) {
        die('Erreur de préparation de la requête de détail: ' . $conn->error);
    }

    $stmt_details->bind_param("iiid", $order_id, $product['id_product'], $product['quantite'], $product['total']);
    $stmt_details->execute();
}

// حذف المنتجات من السلة بعد تأكيد الطلب
$sql_delete = "DELETE FROM panier WHERE id_user = ?";
$stmt_delete = $conn->prepare($sql_delete);

// التحقق من نجاح التحضير
if ($stmt_delete === false) {
    die('Erreur de préparation de la requête de suppression: ' . $conn->error);
}

$stmt_delete->bind_param("i", $id_user);
$stmt_delete->execute();

$stmt_order->close();
$stmt_details->close();
$stmt_delete->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de la Commande</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Confirmation de votre commande</h1>
    
    <script>
        Swal.fire({
            title: 'Commande Confirmée!',
            text: 'Votre commande a été validée avec succès.',
            icon: 'success',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../html/home.php";  // العودة إلى الصفحة الرئيسية
            }
        });
    </script>
</body>
</html>
