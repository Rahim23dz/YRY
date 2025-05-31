<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color:red;'>Veuillez vous connecter pour voir vos commandes.</p>";
    exit;
}

$id_user = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="../css/mes_commandes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header>
  <!-- القسم العلوي -->
  <div class="header-top">
    <div class="header-left">
      <img src="../img/logo.jpg" alt="Logo YRY">
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

        // زر Besoin d'aide
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

    <!-- زر أزرق كـ button -->
    <button class="btn-blue" style="background-color: orange;" onclick="location.href='../html/contact_support.html'">Support</button>
  </div>

  <div class="header-bottom">
    <nav class="header-nav" id="navMenu">
      <!-- زر أزرق كـ button -->
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


    <h1>📦 Mes Commandes</h1>

<?php
$sql = "SELECT * FROM commandes WHERE id_user = ? ORDER BY date_commande DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p style='text-align:center;'>Vous n'avez encore passé aucune commande.</p>";
} else {
    while ($commande = $result->fetch_assoc()) {
        echo "<div class='container'>";
        echo "<h3>Commande #" . $commande['id_commande'] . " - Date: " . $commande['date_commande'] . "</h3>";
        echo "<p><strong>Total:</strong> " . number_format($commande['total'], 2) . " DZD</p>";

        // Affichage de l'état de la commande
        $statut = htmlspecialchars($commande['statut']);
        echo "<p><strong>Statut de la commande:</strong> $statut</p>";

        // Détails de cette commande
        $sqlDetails = "SELECT dc.*, p.nom, p.prix 
                       FROM details_commande dc
                       JOIN products p ON dc.id_product = p.id_product
                       WHERE dc.id_commande = ?";
        $stmtDetails = $conn->prepare($sqlDetails);
        if (!$stmtDetails) {
            echo "<p style='color:red;'>Erreur lors de la préparation des détails: " . $conn->error . "</p>";
            continue;
        }

        $stmtDetails->bind_param("i", $commande['id_commande']);
        $stmtDetails->execute();
        $detailsResult = $stmtDetails->get_result();

        echo "<table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>";

        while ($detail = $detailsResult->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($detail['nom']) . "</td>
                    <td>" . number_format($detail['prix'], 2) . " DZD</td>
                    <td>" . $detail['quantite'] . "</td>
                    <td>" . number_format($detail['total'], 2) . " DZD</td>
                  </tr>";
        }

        echo "</tbody></table>";
        echo "</div><br>";
        $stmtDetails->close();
    }
}
$stmt->close();
$conn->close();
?>

</body>
</html>
