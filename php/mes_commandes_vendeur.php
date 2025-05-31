<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) ) {
    // تخزين رسالة الخطأ فـ SESSION باش نعرضوها في صفحة login
    $_SESSION['error_message'] = "⛔ يجب أن تسجّل الدخول كبائع للوصول إلى هذه الصفحة.";
    header("Location: ../html/login.php");
    exit;
}

$id_vendeur = $_SESSION['user_id'];

// استعلام لجلب الطلبات حسب البائع
$sql = "SELECT 
            c.id_commande, 
            c.date_commande,
            c.statut,
            u.username, 
            u.phone,
            p.nom AS nom_produit, 
            p.prix,
            dc.quantite,
            a.adresse_exacte,
            w.nom_wilaya
        FROM commandes c
        JOIN details_commande dc ON c.id_commande = dc.id_commande
        JOIN products p ON dc.id_product = p.id_product
        JOIN users u ON c.id_user = u.id_user
        LEFT JOIN address a ON u.id_user = a.user_id
        LEFT JOIN wilaya w ON a.id_wilaya = w.id_wilaya
        WHERE p.id_vendeur = ?
        ORDER BY c.date_commande DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vendeur);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes - Vendeur</title>
    <link rel="stylesheet" href="../css/commandes_vndr.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>

<header>
  <div class="header-top">
    <div class="header-left">
      <img src="../img/logo.jpg" alt="Logo YRY" style="height:50px;">
      <h1>YRY</h1>
    </div>

<div class="search-box">
  <input type="text" id="searchBar" placeholder="Rechercher un produit, une categorie ou une marque ....">
  <span class="search-icon">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
      <circle cx="11" cy="11" r="7"></circle>
      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
  </span>
</div>

<div class="header-actions">
  <?php
    if (isset($_SESSION['username'])) {
      echo '<a href="../php/profil.php" class="btn-header icon-btn">
              <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
                  <circle cx="12" cy="7" r="4"></circle>
                  <path d="M5.5 21a7.5 7.5 0 0 1 13 0"></path>
                </svg>
              </span>
              <span class="label">Profil</span>
            </a>';
    } else {
      echo '<a href="../html/login.html" class="btn-header icon-btn">
              <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
                  <path d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
                  <path d="M6 21v-2c0-2.21 3.58-4 6-4s6 1.79 6 4v2"></path>
                </svg>
              </span>
              <span class="label">Se connecter</span>
            </a>';
    }

    if (isset($_SESSION['role']) && $_SESSION['role'] !== 'vendeur') {
      $panierCount = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;

      echo '<a href="panier.php" class="btn-header icon-btn panier-btn">
              <span class="icon">
                <i class="fa-solid fa-bag-shopping"></i>';

      if ($panierCount > 0) {
        echo '<span class="badge">' . $panierCount . '</span>';
      }

      echo '</span>
            <span class="label">Panier</span>
          </a>';
    }

    echo '<a href="../html/Aide & Contact.php" class="btn-header icon-btn besoin-aide-btn">
            <span class="icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
                <path d="M12 18h.01"></path>
                <path d="M12 14a4 4 0 1 0-4-4"></path>
                <path d="M12 2a10 10 0 1 1-10 10"></path>
              </svg>
            </span>
            <span class="label">Besoin d\'aide</span>
          </a>';
  ?>
</div>

<button class="btn-blue" style="background-color: orange;" onclick="location.href='../html/contact_support.html'">Support</button>


  </div>

  <div class="header-bottom">
    <nav class="header-nav" id="navMenu">
      <button class="btn-blue" onclick="location.href='../html/home.php'">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;" viewBox="0 0 24 24">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
        Acceuil
      </button>


  <a href="../html/amortisseur.php">Amortisseurs</a>
  <a href="../html/disques_de_frein.php">Disques de frein</a>
  <a href="../html/batterie.php">Batterie</a>
  <a href="../html/pneu.php">Pneu</a>
  <a href="../html/bougie.php">Bougies</a>
  <a href="../html/Capteur.php">Capteurs</a>
  <a href="../html/huile.php">Huile</a>

  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendeur'): ?>
    <a href="../html/vendeur_dashboard.php">
      <button class="btn-vendeur">Page Vendeur</button>
    </a>
  <?php endif; ?>
</nav>


  </div>
</header>

<div class="container">
    <h2>Liste des Commandes reçues</h2>


<?php if ($result->num_rows > 0): ?>
   <table>
<thead>
    <tr>
        <th>Nom Client</th>
        <th>Téléphone</th>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Wilaya</th>
        <th>Adresse</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['nom_produit']) ?></td>
        <td><?= htmlspecialchars($row['prix']) ?> DA</td>
        <td><?= htmlspecialchars($row['quantite']) ?></td>
        <td><?= htmlspecialchars($row['nom_wilaya'] ?? 'Non défini') ?></td>
        <td><?= htmlspecialchars($row['adresse_exacte'] ?? 'Non défini') ?></td>
        <td><?= htmlspecialchars($row['date_commande']) ?></td>
        <td><?= htmlspecialchars($row['statut']) ?></td>
        <td>
          <?php if ($row['statut'] == 'en_attente'): ?>
                                <form method="post" action="traiter_commande.php" style="display:inline;">
    <input type="hidden" name="id_commande" value="<?= $row['id_commande']; ?>">
    <input type="hidden" name="action" value="accepter">
    <button type="submit" class="btn-confirmer" onclick="return confirm('Confirmer l\'acceptation ?')">✅ Confirmer</button>
</form>
<form method="post" action="traiter_commande.php" style="display:inline;">
    <input type="hidden" name="id_commande" value="<?= $row['id_commande']; ?>">
    <input type="hidden" name="action" value="refuser">
    <button type="submit" class="btn-refuser" onclick="return confirm('Confirmer le refus ?')">❌ Refuser</button>
</form>

                            <?php else: ?>
                                Commande traitée
                            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>


</table>  
    <?php else: ?>
        <p class="empty">Aucune commande reçue pour le moment.</p>
    <?php endif; ?>
</div>

</body>
</html>  