<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

$sql = "SELECT v.id_vendeur, v.username, v.email, v.phone, v.profile_image, v.store_name, v.store_address, v.date_inscription, v.status, w.nom_wilaya
        FROM vendeurs v
        LEFT JOIN wilaya w ON v.id_wilaya = w.id_wilaya
        ORDER BY v.date_inscription DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Gérer Vendeurs</title>
<link rel="stylesheet" href="../css/gerer_vendeurs.css" />
</head>
<body>

<div class="main-container">
  <div class="header-content">
    <div class="header-title">
      <h1>Gérer les Vendeurs</h1>
    </div>
    <div class="header-actions">
      <a href="../html/admin_dashboard.php" class="btn-back">Retour à l'Espace Vendeur</a>
    </div>
  </div>

  <div class="table-wrapper">
    <div class="table-header">
      <h2 class="table-title">
        Liste des vendeurs
        <span class="table-count"><?php echo $result->num_rows; ?></span>
      </h2>
    </div>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Image de Profil</th>
            <th>Nom du magasin</th>
            <th>Adresse du magasin</th>
            <th>Wilaya</th>
            <th>Date d'inscription</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($vendeur = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($vendeur['id_vendeur']); ?></td>
            <td><?php echo htmlspecialchars($vendeur['username']); ?></td>
            <td><?php echo htmlspecialchars($vendeur['email']); ?></td>
            <td><?php echo htmlspecialchars($vendeur['phone'] ?? '-'); ?></td>
            <td>
              <?php 
                $defaultImage = '../uploads/profil/default.jpg';
                $profileImage = $vendeur['profile_image'] ?? '';

                if (!empty($profileImage) && file_exists('../uploads/profil/' . $profileImage)): ?>
                  <img src="../uploads/profil/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-img" />
                <?php else: ?>
                  <img src="<?php echo $defaultImage; ?>" alt="Default Profile Image" class="profile-img" />
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($vendeur['store_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($vendeur['store_address'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($vendeur['nom_wilaya'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($vendeur['date_inscription']); ?></td>
            <td>
              <?php 
                $status_raw = $vendeur['status'] ?? 'en_attente';
                $status = strtolower(trim($status_raw));
                switch ($status) {
                    case 'accepte':
                    case 'accepté':
                        $class_status = 'accepté';
                        break;
                    case 'confirme':
                    case 'confirmé':
                        $class_status = 'confirmé';
                        break;
                    case 'refuse':
                    case 'refusé':
                        $class_status = 'refusé';
                        break;
                    default:
                        $class_status = 'en_attente';
                }
              ?>
              <span class="status <?php echo $class_status; ?>">
                <?php echo ucfirst($class_status); ?>
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <a href="modifier_vendeur.php?id_vendeur=<?php echo $vendeur['id_vendeur']; ?>" class="btn-edit">Modifier</a>
                <form method="post" action="supprimer_vendeur.php" onsubmit="return confirm('Voulez-vous vraiment supprimer ce vendeur ?');" style="display:inline;">
                  <input type="hidden" name="id_vendeur" value="<?php echo $vendeur['id_vendeur']; ?>">
                  <!-- TODO: أضف توكن CSRF هنا -->
                  <button type="submit" class="btn-delete">Supprimer</button>
                </form>
                <a href="voir_vendeur.php?id_vendeur=<?php echo $vendeur['id_vendeur']; ?>" class="btn-view">Voir reçus</a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="../js/compte_status.js"></script>
</body>
</html>
