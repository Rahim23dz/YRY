<?php
session_start();
require_once '../php/db.php'; // رابط صحيح لاتصال قاعدة البيانات

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Amortisseurs - YRY</title>
  <link rel="stylesheet" href="../css/amortisseur.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- Header -->
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

    <div class="tooltip">
  <button class="btn-blue" style="background-color: orange;">Support</button>
  <span class="tooltiptext">Coming Soon!</span>
</div> <style>
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

      <a href="amortisseur.php">Amortisseurs</a>
      <a href="disques_de_frein.php">Disques de frein</a>
      <a href="batterie.php">Batterie</a>
      <a href="pneu.php">Pneu</a>
      <a href="bougie.php">Bougies</a>
      <a href="Capteur.php">Capteurs</a>
      <a href="huile.php">Huile</a>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendeur'): ?>
        <a href="../html/vendeur_dashboard.php">
          <button class="btn-vendeur">Page Vendeur</button>
        </a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Main Content -->
<main>
  <h2>Nos Démarreurs</h2>

  <label for="vehicule">Choisissez votre véhicule:</label>
  <select id="vehicule" name="vehicule">
    <option value="all">Tous les véhicules</option>
    <?php
    $vehicules = mysqli_query($conn, "SELECT * FROM vehicules");
    while ($v = mysqli_fetch_assoc($vehicules)) {
      echo '<option value="'.$v['id_vehicule'].'">'.htmlspecialchars($v['marque'].' '.$v['modele']).'</option>';
    }
    ?>
  </select>

  <label for="wilaya">Choisissez la wilaya:</label>
  <select id="wilaya" name="wilaya">
    <option value="all">Toutes les Wilayas</option>
    <?php
    $wilayas = mysqli_query($conn, "SELECT id_wilaya, nom_wilaya FROM wilaya ORDER BY nom_wilaya ASC");
    if ($wilayas && mysqli_num_rows($wilayas) > 0) {
      while ($w = mysqli_fetch_assoc($wilayas)) {
        echo '<option value="'.$w['id_wilaya'].'">'.htmlspecialchars($w['nom_wilaya']).'</option>';
      }
    } else {
      echo '<option value="all">Aucune wilaya trouvée</option>';
    }
    ?>
  </select>
  <label for="prix_min">Prix min:</label>
<input type="number" id="prix_min" placeholder="السعر الأدنى" />
<input type="number" id="prix_max" placeholder="السعر الأقصى" />
<button id="filtrerBtn">🔍 فلترة</button>


 

  <div id="products-container" class="produits-liste" data-sous-type="68">
    <!-- سيتم تحميل المحتوى عبر AJAX -->
  </div>

  <p id="noResultMessage" style="display:none; text-align:center; color: gray; margin-top: 20px;">
    Aucun produit trouvé.
  </p>
</main>

<!-- Footer -->
<footer>
  <p>&copy; 2025 YRY. Tous droits réservés.</p>
</footer>

<!-- JavaScript -->
<script src="../js/batterie.js"></script> <!-- ملف جافاسكريبت خاص بالأمورتيسير -->

</body>
</html>
