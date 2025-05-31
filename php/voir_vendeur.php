<?php
session_start();
include '../php/db.php';

// ضبط ترميز الاتصال
$conn->set_charset("utf8mb4");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// جلب id_vendeur من الرابط GET مع التحقق
if (!isset($_GET['id_vendeur']) || !is_numeric($_GET['id_vendeur'])) {
    die("ID du vendeur manquant ou invalide.");
}

$id_vendeur = intval($_GET['id_vendeur']);

// جلب بيانات البائع
$sql = "SELECT v.id_vendeur, v.username, v.email, v.phone, v.profile_image, v.store_name, v.store_address, v.date_inscription, w.nom_wilaya, v.status, v.recus
        FROM vendeurs v
        LEFT JOIN wilaya w ON v.id_wilaya = w.id_wilaya
        WHERE v.id_vendeur = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vendeur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Vendeur non trouvé.");
}

$vendeur = $result->fetch_assoc();

// معالجة قبول أو رفض الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $valid_statuses = ['accepte', 'refuse'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $status = 'accepte';
    } elseif ($action === 'refuse') {
        $status = 'refuse';

        // حذف ملف الإيصال من السيرفر إذا موجود
        if (!empty($vendeur['recus']) && file_exists('../' . $vendeur['recus'])) {
            unlink('../' . $vendeur['recus']);
        }

        // تحديث الحقل recus إلى NULL في قاعدة البيانات
        $clear_recu_sql = "UPDATE vendeurs SET recus = NULL WHERE id_vendeur = ?";
        $clear_recu_stmt = $conn->prepare($clear_recu_sql);
        $clear_recu_stmt->bind_param("i", $id_vendeur);
        $clear_recu_stmt->execute();
    } else {
        $status = $vendeur['status']; // بدون تغيير
    }

    if (in_array($status, $valid_statuses)) {
        $update_sql = "UPDATE vendeurs SET status = ? WHERE id_vendeur = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $status, $id_vendeur);

        if ($update_stmt->execute()) {
            header("Location: voir_vendeur.php?id_vendeur=" . $id_vendeur);
            exit();
        } else {
            echo "<p style='color:red;'>Erreur lors de la mise à jour du statut.</p>";
        }
    } else {
        echo "<p style='color:red;'>Action invalide.</p>";
    }
}

function getStatusClass($status) {
    $map = [
        'accepte' => 'accepté',
        'refuse' => 'refusé',
        'en_attente' => 'en_attente'
    ];
    return $map[$status] ?? 'en_attente';
}

function getStatusLabel($status) {
    $labels = [
        'accepte' => 'Accepté',
        'refuse' => 'Refusé',
        'en_attente' => 'En attente'
    ];
    return $labels[$status] ?? 'En attente';
}
?>

<!DOCTYPE html>
<html lang="fr">
  <link rel="stylesheet" href="../css/voir_vendeur.css" />
<head>

<meta charset="UTF-8" />
<title>Détails du Vendeur</title>

<script>
  const vendeurId = <?php echo json_encode($id_vendeur); ?>;
</script>
<script src="../js/compte_status.js"></script>

</head>
<body>

<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Détails du Vendeur</h1>
        </div>
        <a href="liste_vendeurs.php" class="back-link">
            Retour à la liste des vendeurs
        </a>
    </div>
</div>

<div class="container">
    <section class="profile-section">
        <div class="profile-image-container">
         <?php 
                $defaultImage = '../uploads/profil/default.jpg';
                $profileImage = $vendeur['profile_image'] ?? '';

                if (!empty($profileImage) && file_exists('../uploads/profil/' . $profileImage)): ?>
                  <img src="../uploads/profil/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-img" />
                <?php else: ?>
                  <img src="<?php echo $defaultImage; ?>" alt="Default Profile Image" class="profile-img" />
                <?php endif; ?>

        </div>
        <h2 class="vendor-name"><?php echo htmlspecialchars($vendeur['username']); ?></h2>
        <p class="vendor-email"><?php echo htmlspecialchars($vendeur['email']); ?></p>
    </section>

    <section class="info-section">
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 3.98c.329.294.77.545 1.255.72a.795.795 0 0 0 .637.061l1.04-.413c.69-.273 1.278-.159 1.704.115l2.487 1.462a1.745 1.745 0 0 1 .163 2.611l-3.094 3.094c-.133.133-.553.056-.679-.097l-1.46-2.487a1.005 1.005 0 0 0-.513-.637l-.72 1.255a.795.795 0 0 1-.061.637l.413 1.04c.272.69.158 1.278-.115 1.704L1.885 15.489a1.745 1.745 0 0 1-2.61-.163L.163 11.49c-.329-.294-.77-.545-1.255-.72a.795.795 0 0 0-.637-.061l-1.04.413c-.69.273-1.278.159-1.704-.115l-2.487-1.462a1.745 1.745 0 0 1-.163-2.611l3.094-3.094c.133-.133.553-.056.679.097l1.46 2.487a1.005 1.005 0 0 0 .513.637l.72-1.255a.795.795 0 0 1 .061-.637l-.413-1.04c-.272-.69-.158-1.278.115-1.704L1.885.511z"/>
                    </svg>
                    Téléphone
                </div>
                <div class="info-value"><?php echo htmlspecialchars($vendeur['phone'] ?? '-'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shop-fill" viewBox="0 0 16 16">
                        <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 2.25 5.625v-.255a1.5 1.5 0 0 1 .334-1.098l2.608-3.045z"/>
                        <path d="M2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H2z"/>
                    </svg>
                    Nom du magasin
                </div>
                <div class="info-value"><?php echo htmlspecialchars($vendeur['store_name'] ?? '-'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                    </svg>
                    Adresse du magasin
                </div>
                <div class="info-value"><?php echo htmlspecialchars($vendeur['store_address'] ?? '-'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                        <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.155-1.432.399-2.258.762A7 7 0 0 0 1 8c0 .007.002.013.002.02l.398.202A7 7 0 0 0 6.41 9.392c.232.044.474.086.721.125l.232-.147a.5.5 0 0 1 .158-.113l.615-.225a.5.5 0 0 1 .042-.027c.145-.053.297-.099.459-.139l.644.079a.5.5 0 0 1 .095.083c.075.053.154.108.236.169l.343-.366a.5.5 0 0 1 .021-.032c.137-.17.296-.307.473-.415A7 7 0 0 0 15 8a7 7 0 0 0-7.5-6.923z"/>
                    </svg>
                    Wilaya
                </div>
                <div class="info-value"><?php echo htmlspecialchars($vendeur['nom_wilaya'] ?? '-'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-fill" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1zm0 1V2h12V1H3.5z"/>
                    </svg>
                    Date d'inscription
                </div>
                <div class="info-value"><?php echo htmlspecialchars(date('d/m/Y', strtotime($vendeur['date_inscription']))); ?></div>
            </div>
        </div>
    </section>

    <section class="status-section">
        <div class="status-container">
            <div class="status-info">
                <h3 class="status-label">Statut du compte:</h3>
                <span class="status <?php echo getStatusClass($vendeur['status']); ?>">
                    <?php echo getStatusLabel($vendeur['status']); ?>
                </span>
            </div>
        </div>
    </section>

    <section class="receipt-section">
        <h2 class="receipt-title">
            Reçu de paiement
        </h2>
        <div class="receipt-container">
            <?php if (!empty($vendeur['recus']) && file_exists('../' . $vendeur['recus'])): ?>
                <?php
                $file_path = '../' . $vendeur['recus'];
                $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="<?php echo htmlspecialchars($file_path); ?>" alt="Reçu de paiement" class="receipt-image" />
                    <a href="<?php echo htmlspecialchars($file_path); ?>" class="receipt-link" target="_blank">
                        Voir le reçu
                    </a>
                <?php elseif ($file_extension === 'pdf'): ?>
                    <embed src="<?php echo htmlspecialchars($file_path); ?>" type="application/pdf" width="100%" height="400px" />
                    <a href="<?php echo htmlspecialchars($file_path); ?>" class="receipt-link" target="_blank">
                        Télécharger le PDF
                    </a>
                <?php else: ?>
                    <p class="no-receipt">Format de fichier non supporté.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-receipt">Aucun reçu n'a été soumis.</p>
            <?php endif; ?>
        </div>
    </section>

   <section class="actions-section">
    <h2 class="actions-title">Actions</h2>
    <form method="post" class="actions-form">
        <button type="submit" name="action" value="accept" class="btn btn-accept">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            Accepter
        </button>
        <button type="submit" name="action" value="refuse" class="btn btn-refuse">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
            </svg>
            Refuser
        </button>
    </form>
</section>

</div>

</body>
</html>
