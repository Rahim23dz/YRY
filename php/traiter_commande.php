<?php
session_start();
include 'db.php';

// التحقق من وجود البيانات الأساسية
if (!isset($_POST['id_commande'], $_POST['action'], $_SESSION['user_id'])) {
    header("Location: ../html/vendeur_dashboard.php?error=missing_data");
    exit;
}

$id_commande = intval($_POST['id_commande']);
$action = $_POST['action'];
$id_vendeur = intval($_SESSION['user_id']);

// التحقق من أن الإجراء صحيح (لمنع إدخال قيم غير متوقعة)
if (!in_array($action, ['accepter', 'refuser'], true)) {
    header("Location: ../html/vendeur_dashboard.php?error=invalid_action");
    exit;
}

// تحويل الإجراء إلى حالة الطلب
$nouveau_statut = ($action === 'accepter') ? 'accepté' : 'refusé';

// التأكد من أن الطلب يعود لهذا البائع
$sql_check = "SELECT c.id_user FROM commandes c
              JOIN details_commande dc ON c.id_commande = dc.id_commande
              JOIN products p ON dc.id_product = p.id_product
              WHERE c.id_commande = ? AND p.id_vendeur = ?";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    error_log("خطأ في تحضير استعلام التحقق: " . $conn->error);
    header("Location: ../html/vendeur_dashboard.php?error=check_prepare_failed");
    exit;
}

$stmt_check->bind_param("ii", $id_commande, $id_vendeur);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    $stmt_check->close();
    header("Location: ../html/vendeur_dashboard.php?error=unauthorized");
    exit;
}

$row_check = $result_check->fetch_assoc();
$id_user_client = intval($row_check['id_user']);
$stmt_check->close();

// تحديث حالة الطلب في قاعدة البيانات
$sql_update = "UPDATE commandes SET statut = ? WHERE id_commande = ?";
$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    error_log("خطأ في تحضير استعلام التحديث: " . $conn->error);
    header("Location: ../html/vendeur_dashboard.php?error=update_prepare_failed");
    exit;
}

$stmt_update->bind_param("si", $nouveau_statut, $id_commande);

if ($stmt_update->execute()) {
    $stmt_update->close();

    // إنشاء إشعار جديد للعميل
    $message = "Commande " . $nouveau_statut;

    $sql_notif = "INSERT INTO notifications (id_user, id_commande, message) VALUES (?, ?, ?)";
    $stmt_notif = $conn->prepare($sql_notif);

    if (!$stmt_notif) {
        error_log("خطأ في تحضير استعلام الإشعار: " . $conn->error);
        header("Location: ../html/vendeur_dashboard.php?error=notif_prepare_failed");
        exit;
    }

    $stmt_notif->bind_param("iis", $id_user_client, $id_commande, $message);

    if (!$stmt_notif->execute()) {
        error_log("خطأ في تنفيذ استعلام الإشعار: " . $stmt_notif->error);
        header("Location: ../html/vendeur_dashboard.php?error=notif_execute_failed");
        exit;
    }

    $stmt_notif->close();
    $conn->close();

    header("Location: ../html/vendeur_dashboard.php?msg=updated");
    exit;
} else {
    error_log("خطأ في تحديث حالة الطلب: " . $stmt_update->error);
    $stmt_update->close();
    $conn->close();
    header("Location: ../html/vendeur_dashboard.php?error=update_failed");
    exit;
}
?>
