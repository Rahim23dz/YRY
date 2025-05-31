<?php
session_start();
include '../php/db.php';

// تحقق إذا كان المستخدم مسجل الدخول وعندو دور vendeur
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'vendeur') {
    header('Location: ../html/login.php');
    exit();
}

// نجيبو معلومات البائع
$username = $_SESSION['username'];
 // تأكد من وجود id_vendeur في الجلسة

// جلب عدد المنتجات الخاصة بالبائع
$queryProducts = $conn->prepare("
    SELECT COUNT(*) AS total_products 
    FROM products 
    WHERE id_vendeur = ?
");
$queryProducts->bind_param("i", $id_vendeur);
$queryProducts->execute();
$resultProducts = $queryProducts->get_result()->fetch_assoc();
$totalProducts = $resultProducts['total_products'] ?? 0;

// جلب عدد الطلبات الخاصة بالبائع
$queryOrders = $conn->prepare("
    SELECT COUNT(DISTINCT od.id_order) AS total_orders 
    FROM order_details od
    JOIN products p ON od.id_product = p.id_product
    WHERE p.id_vendeur = ?
");
$queryOrders->bind_param("i", $id_vendeur);
$queryOrders->execute();
$resultOrders = $queryOrders->get_result()->fetch_assoc();
$totalOrders = $resultOrders['total_orders'] ?? 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Vendeur</title>
    <link rel="stylesheet" href="../css/vendeur_dashboard.css">
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
          echo '<a href="../php/login.html" class="btn-header icon-btn">
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

    <div class="tooltip">
  <button class="btn-blue" style="background-color: orange;">Support</button>
  <span class="tooltiptext">Coming Soon!</span>
 <style>
.tooltip {
  position: relative;
  display: inline-block;
  cursor: pointer;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 6px 8px;
  position: absolute;
  top: 120%; /* تخليها تظهر تحت الزر */
  left: 50%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
  z-index: 10;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
</style>
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

<main>
    <section class="dashboard-cards">
        <div class="card product">
            <h2>Mes Produits</h2>
            <p><?php echo $totalProducts; ?> Produits</p>
            <a href="mes_produits.php" class="btn">Voir mes produits</a>
        </div>

        <div class="card orders">
            <h2>Mes Commandes</h2>
            <p><?php echo $totalOrders; ?> Commandes</p>
            <a href="../php/mes_commandes_vendeur.php" class="btn">Voir mes commandes</a>
        </div>

        <div class="card product">
            <h2>Ajouter un Produit</h2>
            <p>Ajouter un nouveau produit au marché</p>
            <a href="vondeur.php" class="btn">Ajouter</a>
        </div>

        <!-- إضافة الخيارات الجديدة -->
        <div class="card product">
            <h2>Statistiques de Ventes</h2>
            <p>Voir vos ventes récentes et les tendances</p>
            <a href="statistiques_ventes.php" class="btn">Voir les statistiques</a>
        </div>

        <div class="card product">
            <h2>Offres Promotionnelles</h2>
            <p>Créer et gérer des promotions pour vos produits</p>
            <a href="offres_promotionnelles.php" class="btn">Voir les offres</a>
        </div>

        <div class="card product">
            <h2>Gestion des Stocks</h2>
            <p>Suivre les niveaux de stock de vos produits</p>
            <a href="gestion_stocks.php" class="btn">Voir les stocks</a>
        </div>

        <div class="card product">
            <h2>Paramètres de Livraison</h2>
            <p>Gérer vos options de livraison</p>
            <a href="parametres_livraison.php" class="btn">Voir les paramètres</a>
        </div>

        <div class="card product">
            <h2>Rapports</h2>
            <p>Générer des rapports sur vos performances de vente</p>
            <a href="rapports.php" class="btn">Voir les rapports</a>
        </div>

        <div class="card product">
            <h2>Paramètres du Compte</h2>
            <p>Mettre à jour vos informations de compte</p>
            <a href="parametres_compte.php" class="btn">Voir les paramètres</a>
        </div>

        <div class="card product">
            <h2>Paramètres de Notifications</h2>
            <p>Gérer vos notifications</p>
            <a href="notifications.php" class="btn">Voir les notifications</a>
        </div>
    </section>
</main>

</body>
</html>
