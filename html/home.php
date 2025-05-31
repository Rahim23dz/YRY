<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>yry - Pièces de Rechange</title>
  <link rel="stylesheet" href="../css/home.css" />
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
        echo '<a href="../html/login.php" class="btn-header icon-btn">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
              <path d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
              <path d="M6 21v-2c0-2.21 3.58-4 6-4s6 1.79 6 4v2"></path>
            </svg>
          </span>
          <span class="label">Se connecter</span>
        </a>';
    }

 
// نسمح للكل يشوف زر السلة إلا إذا كان البائع (vendeur)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendeur') {
    // عدد العناصر في السلة موجودة في الجلسة 'panier'، وإلا 0
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



    // إذا كان المستخدم بائعًا، أضف زر "Ajouter un produit"
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendeur') {
        echo '<a href="vondeur.php" class="btn-header icon-btn ajouter-produit-btn">
                <span class="icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                </span>
                <span class="label">Ajouter produit</span>
              </a>';
    }

if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    $id_user = $_SESSION['user_id'];

    // هذا الزر يظهر فقط إذا كان الدور هو client
    if ($role === 'client') {
        $notifPage = '../php/notifications.php';

        // جلب عدد الإشعارات غير المقروءة من قاعدة البيانات
        include '../php/db.php';

        $sql_notif = "SELECT COUNT(*) AS total FROM notifications WHERE id_user = ? AND lu = 0";
        $stmt = $conn->prepare($sql_notif);
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifData = $result->fetch_assoc();
        $notificationCount = $notifData['total'];
        $stmt->close();
        $conn->close();

        // عرض الزر
        echo '<a href="' . $notifPage . '" class="btn-header icon-btn notification-btn">
                <span class="icon">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                  </svg>';

        if ($notificationCount > 0) {
            echo '<span class="badge">' . $notificationCount . '</span>';
        }

        echo '</span>
              <span class="label">Notifications</span>
            </a>';
    }
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




  <!-- القسم السفلي -->
   <!-- زر أزرق كـ button -->
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

<div class="tooltip">
  <button class="btn-blue" style="background-color: orange;">Support</button>
  <span class="tooltiptext">Coming Soon!</span>
</div>



  <div class="header-bottom">
    
  <nav class="header-nav" id="navMenu">
    <!-- زر أزرق كـ button -->
<button class="btn-blue" onclick="document.getElementById('accueil').scrollIntoView({ behavior: 'smooth' });">

  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;" viewBox="0 0 24 24">
    <line x1="3" y1="6" x2="21" y2="6"></line>
    <line x1="3" y1="12" x2="21" y2="12"></line>
    <line x1="3" y1="18" x2="21" y2="18"></line>
  </svg>
  Tout nos categories
</button>

    <a href="amortisseur.php">Amortisseurs</a>
    <a href="disques_de_frein.php">Disques de frein</a>
    <a href="batterie.php">Batterie</a>
    <a href="pneu.php">Pneu</a>
    <a href="bougie.php">Bougies</a>
    <a href="Capteur.php">Capteurs</a>
    <a href="huile.php">Huile</a>

   

  

  </nav>
</div>

</header>

<div class="banner-image" style="position: relative; text-align: center; color: white;">
  <img src="../img/selector-hp-bg-desktop.webp" alt="Bannière YRY" style="width: 100%; height: auto;">
  <div class="banner-text" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
       font-size: 2rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">
    Identifiez votre véhicule pour trouver des pièces compatibles
  </div>
</div>
<section class="highlights">
  <div class="highlight-item">
  <i class="fa-solid fa-award"></i>
    <p><strong>Pièces neuves et d'origine</strong><br><span>au meilleur prix</span></p>
  </div>
  <div class="highlight-item">
    <i class="fa-solid fa-tools"></i>
    <p><strong>180 experts en mécanique</strong><br><span>à votre écoute</span></p>
  </div>
  <div class="highlight-item">
<i class="fa-solid fa-book-open"></i>    <p><strong>Le plus grand catalogue</strong><br><span>d'Algérie</span></p>
  </div>
  <div class="highlight-item">
<i class="fa-solid fa-truck-fast"></i>
    <p><strong>Livraison</strong><br><span>chez vous ou en point relais</span></p>
  </div>
</section>
 
<!-- قسم السلايدر مع النقاط -->
<section class="image-slider">
  <div class="slider-wrapper">
    <img src="../img/banner-20250512-fr-promo-huile-shell-xl.webp" class="slider-image active">
    <img src="../img/s.webp" class="slider-image">
    <img src="../img/ss.webp" class="slider-image">
    <img src="../img/f.webp" class="slider-image">
    <img src="../img/r.webp" class="slider-image">
    <img src="../img/banner-20250512-fr-promo-tout-delphi-xl.webp" class="slider-image">
  </div>
  <div class="slider-dots">
    <span class="dot active-dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>
</section>

<style>
.image-slider {
  max-width: 1300px;
  margin: 0 auto;
  text-align: center;
  position: relative;
  padding: 0;
}

.slider-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
  margin: 0;
  padding: 0;
}

.slider-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slider-image.active {
  opacity: 1;
}

.slider-dots {
  margin: 5px 0 15px;
}

.dot {
  display: inline-block;
  height: 10px;
  width: 10px;
  margin: 0 6px;
  background-color: #bbb;
  border-radius: 50%;
  transition: background-color 1s;
  cursor: pointer;
}

.active-dot {
  background-color: #004d40;
}
</style>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.slider-image');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.toggle('active', i === index);
  });
  dots.forEach((dot, i) => {
    dot.classList.toggle('active-dot', i === index);
  });
}

dots.forEach((dot, index) => {
  dot.addEventListener('click', () => {
    currentSlide = index;
    showSlide(currentSlide);
  });
});

setInterval(() => {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}, 4000);
</script>








  <section id="accueil">
    <!-- Groupe: Direction / Suspension / Train -->
    <div class="produits-groupe">
  <h2>Direction / Suspension / Train</h2>
  <div class="produits">

    <div class="produit">
      <h4>Amortisseurs</h4>
      <a href="amortisseur.php">
        <img src="../img/amrt.jpg" alt="Amortisseurs">
      </a>
      <p>Amortisseurs de qualité supérieure...</p>
    </div>
    
    <div class="produit">
      <h4>Suspension d’essieux</h4>
      <a href="Suspension_d_essieux.php">
        <img src="../img/Suspension d’essieux.jpg" alt="Suspension d’essieux robuste">
      </a>
      <p>Suspension d’essieux robuste pour une performance optimale.</p>
    </div>
    
    <div class="produit">
      <h4>Transmission</h4>
      <a href="transmission.php">
        <img src="../img/Transmission.jpg" alt="">
      </a>
      <p>Composants de transmission pour une meilleure performance du véhicule.</p>
    </div>

    <div class="produit">
      <h4>Rotules / Direction</h4>
      <a href="rotule.php">
        <img src="../img/rotule.jpg" alt="">
      </a>
      <p>Rotules et pièces de direction pour une conduite précise et stable.</p>
    </div>

    <div class="produit">
      <h4>Moyeux et roulements</h4>
      <a href="roulement.php">
        <img src="../img/roulement.jpg" alt="">
      </a>
      <p>Roulements et moyeux pour un fonctionnement fluide de votre véhicule.</p>
    </div>

    <!-- إضافات جديدة -->
    <div class="produit">
      <h4>Direction</h4>
      <a href="direction.php">
        <img src="../img/diriction.jpg" alt="Direction">
      </a>
      <p>Pièces et composants pour la direction du véhicule.</p>
    </div>

    <div class="produit">
      <h4>Butées</h4>
      <a href="butees.php">
        <img src="../img/butees.jpeg" alt="Butées">
      </a>
      <p>Butées de qualité pour une suspension fiable.</p>
    </div>

    <div class="produit">
      <h4>Ressorts et Soufflets</h4>
      <a href="ressorts_et_soufflets.php">
        <img src="../img/ressorts-et-soufflets.avif" alt="Ressorts et Soufflets">
      </a>
      <p>Ressorts et soufflets robustes pour une suspension optimale.</p>
    </div>

  </div>
</div>

    <!-- Groupe: Freinage -->
    <div class="produits-groupe">
      <h2>Freinage</h2>
      <div class="produits">
        <div class="produit">
          <h4>Plaquettes de frein</h4>
          <a href="Plaquettes_de_frein.php">
          <img src="../img/plaquette.jpg" alt=""></a>
          <p>Plaquettes de frein résistantes pour un freinage fiable et constant.</p>
        </div>
        <div class="produit">
          <h4>Disques de frein</h4><a href="Disques_de_frein.php">
          <img src="../img/disque.jpg "alt="Disques de frein sous haute température">
        </a>
          <p>Disques robustes pour un freinage sûr et efficace.</p>
        </div>
        <div class="produit">
          <h4>Hydraulique</h4>
          <a href="hydrauliqe.php">
          <img src="../img/hydraulique.jpg" alt=""></a>
          <p>Systèmes hydrauliques de freinage pour une puissance optimale.</p>
        </div>
        <div class="produit">
          <h4>Freins à tambours</h4>
          <a href="freintomb.php">
          <img src="../img/freinalombours.jpg" alt=""></a>
          <p>Composants pour freins à tambours fiables et performants.</p>
        </div>
        <div class="produit">
          <h4>Capteurs et câbles de freinage</h4>
          <a href="capteur.php">
          <img src="../img/capteur.jpg" alt=""></a>
          <p>Capteurs pour une surveillance précise du système de freinage.</p>
        </div>
        <div class="produit">
          <h4>Assistance au freinage</h4>
          <a href="assisstance.php">
          <img src="../img/assisstent.jpg" alt=""></a>
          <p>Systèmes d’assistance au freinage pour une sécurité accrue.</p>
        </div>
        <div class="produit">
          <h4>Outils freinage</h4>
          <a href="outils.php">
          <img src="../img/outill.jpg"alt=""></a>
          <p>Outils spécialisés pour le montage et la maintenance des systèmes de freinage.</p>
        </div>
      </div>
    </div>

    <!-- Groupe: Entretien Moteur -->
    <div class="produits-groupe">
  <h2>Entretien Moteur</h2>
  <div class="produits">

    <div class="produit">
      <h4>Filtre à huile</h4>
      <a href="filtre_a_huile.php">
        <img src="../img/filtreahuie.jpg" alt="">
      </a>
      <p>Compatible avec plusieurs marques. Haute performance et durabilité.</p>
    </div>

    <div class="produit">
      <h4>Batterie 12V</h4>
      <a href="batterie.php">
        <img src="../img/batterie.jpg" alt="">
      </a>
      <p>Longue durée de vie et démarrage puissant.</p>
    </div>

    <div class="produit">
      <h4>Huile moteur 5W-30</h4>
      <a href="huile.php">
        <img src="../img/huile.jpg" alt="">
      </a>
      <p>Huile moteur synthétique pour une performance optimale et une protection contre l'usure.</p>
    </div>

    <div class="produit">
      <h4>Filtre à air</h4>
      <a href="filter_a_air.php">
        <img src="../img/filtre air.jpg" alt="">
      </a>
      <p>Filtre à air de haute qualité pour une performance optimale du moteur.</p>
    </div>

    <div class="produit">
      <h4>Kit de courroie de distribution</h4>
      <a href="kit_de_courroie.php">
        <img src="../img/kit.jpg" alt="">
      </a>
      <p>Kit complet pour le remplacement de la courroie de distribution, compatible avec de nombreuses marques.</p>
    </div>

    <!-- الإضافات الجديدة -->
    <div class="produit">
      <h4>Pompes</h4>
      <a href="pompes.php">
        <img src="../img/pompes.jpg" alt="Pompes">
      </a>
      <p>Pompes fiables pour différents systèmes moteur.</p>
    </div>

    <div class="produit">
      <h4>Supports moteur</h4>
      <a href="supports_moteur.php">
        <img src="../img/supports-moteur.webp" alt="Supports moteur">
      </a>
      <p>Supports moteur robustes pour une meilleure stabilité.</p>
    </div>

    <div class="produit">
      <h4>Courroies et Distribution</h4>
      <a href="courroies-et-distribution.php">
        <img src="../img/courroies.jpeg" alt="Courroies et Distribution">
      </a>
      <p>Courroies et composants de distribution pour une performance optimale.</p>
    </div>

    <div class="produit">
      <h4>Refroidissement moteur</h4>
      <a href="refroidissement_moteur.php">
        <img src="../img/refroidissementrefroidissement.jpeg" alt="Refroidissement moteur">
      </a>
      <p>Systèmes de refroidissement efficaces pour moteur.</p>
    </div>

    <div class="produit">
      <h4>Injection carburation</h4>
      <a href="injection_carburation.php">
        <img src="../img/injection.jpg" alt="Injection carburation">
      </a>
      <p>Composants pour injection et carburation performants.</p>
    </div>

    <div class="produit">
      <h4>Capteurs et câbles moteur</h4>
      <a href="capteurs_et_cables_moteur.php">
        <img src="../img/capteurs.jpeg" alt="Capteurs et câbles moteur">
      </a>
      <p>Capteurs et câbles pour contrôle moteur précis.</p>
    </div>

    <div class="produit">
      <h4>Turbo</h4>
      <a href="turbo.php">
        <img src="../img/turbo.jpg" alt="Turbo">
      </a>
      <p>Turbo pour amélioration de la puissance moteur.</p>
    </div>

  </div>
</div>

    <!-- Groupe: Divers -->
    <div class="produits-groupe">
      <h2>Divers</h2>
      <div class="produits">
        <div class="produit">

          <h4>Kit de bougies d'allumage</h4>
          <a href="bougie.php">
          <img src="../img/bougie.jpg" alt=""></a>
          <p>Kit de bougies d'allumage pour une meilleure performance du moteur et un démarrage plus rapide.</p>
        </div>
        <div class="produit">
          <h4>Pompe à eau</h4>
          <a href="pompes_eau.php">
          <img src="../img/pompe_eua.jpg"alt=""></a>
          <p>Pompe à eau pour assurer la circulation du liquide de refroidissement et éviter la surchauffe du moteur.</p>
        </div>
        <div class="produit">
          <h4>Boîtier de filtre à carburant</h4>
          <a href="boitier_de_filtre.php">
          <img src="../img/liga3dt.jpg" alt=""></a>
          <p>Filtre à carburant pour améliorer la performance et la longévité de votre moteur.</p>
        </div>
        <div class="produit">
          <h4>Rétroviseur extérieur</h4>
          <a href="retroviseur.php">
          <img src="../img/retroviseur.jpg" alt=""></a>
          <p>Rétroviseur extérieur pour une meilleure visibilité et sécurité.</p>
        </div>
        <div class="produit">
          <h4>Silencieux d'échappement</h4>
          <a href="silencieux.php">
          <img src="../img/shakman.jpg" alt=""></a>
          <p>Silencieux d'échappement pour réduire le bruit et améliorer la performance du véhicule.</p>
        </div>
      </div>
    </div>

    <div class="produits-groupe">
  <h2>Embrayage et Boîte de vitesse</h2>
  <div class="produits">

    <div class="produit">
      <h4>Embrayage et Volant-moteur</h4>
      <a href="embrayage_et_volant_moteur.php">
        <img src="../img/embrayage-volant-moteur.jpg" alt="Embrayage et Volant-moteur">
      </a>
      <p>Pièces pour embrayage et volant moteur de haute qualité pour une performance optimale.</p>
    </div>

    <div class="produit">
      <h4>Accessoires de boîte de vitesse</h4>
      <a href="accessoires_boite_vitesse.php">
        <img src="../img/accessoires-boite-vitesse.jpg" alt="Accessoires de boîte de vitesse">
      </a>
      <p>Accessoires et pièces pour boîte de vitesse adaptés à tous les véhicules.</p>
    </div>

    <div class="produit">
      <h4>Autres pièces d'Embrayage</h4>
      <a href="autres_pieces_embrayage.php">
        <img src="../img/autres-pieces-embrayage.jpeg" alt="Autres pièces d'Embrayage">
      </a>
      <p>Autres composants d'embrayage pour garantir la longévité et la fiabilité.</p>
    </div>

  </div>
</div>

<div class="produits-groupe">
  <h2>Optiques / Phares / Ampoules</h2>
  <div class="produits">

    <div class="produit">
      <h4>Optiques et Phares</h4>
      <a href="optiques_et_phares.php">
        <img src="../img/optiques-phares.jpeg" alt="Optiques et Phares">
      </a>
      <p>Projecteurs et phares avant pour une excellente visibilité nocturne et sécurité renforcée.</p>
    </div>

    <div class="produit">
      <h4>Ampoules, Éclairage avant</h4>
      <a href="ampoules_eclairage_avant.php">
        <img src="../img/ampoules-avant.jpg" alt="Ampoules Éclairage Avant">
      </a>
      <p>Ampoules pour phares avant : puissance et durabilité pour une conduite sécurisée.</p>
    </div>

    <div class="produit">
      <h4>Ampoules, Éclairage arrière</h4>
      <a href="ampoules_eclairage_arriere.php">
        <img src="../img/ampoules-arriere.jpeg" alt="Ampoules Éclairage Arrière">
      </a>
      <p>Éclairage arrière pour une meilleure visibilité de nuit et signalisation claire.</p>
    </div>

    <div class="produit">
      <h4>Ampoules, Éclairage intérieur et signalisation</h4>
      <a href="ampoules_eclairage_interieur_signalisation.php">
        <img src="../img/ampoules-interieur.jpg" alt="Éclairage intérieur et signalisation">
      </a>
      <p>Ampoules pour intérieur et clignotants, combinant efficacité et design moderne.</p>
    </div>

  </div>
</div>

<div class="produits-groupe">
  <h2>Essuie-glaces et pièces</h2>
  <div class="produits">

    <div class="produit">
      <h4>Moteur d'essuie-glace</h4>
      <a href="moteur_essuie_glace.php">
        <img src="../img/moteur-essuie-glace.jpeg" alt="Moteur d'essuie-glace">
      </a>
      <p>Moteur d'essuie-glace puissant pour une visibilité optimale en toute saison.</p>
    </div>

    <div class="produit">
      <h4>Pompe de lave-glace</h4>
      <a href="pompe_lave_glace.php">
        <img src="../img/pompe-lave-glace.png" alt="Pompe de lave-glace">
      </a>
      <p>Pompe efficace pour un nettoyage parfait du pare-brise.</p>
    </div>

    <div class="produit">
      <h4>Lave-glace</h4>
      <a href="lave_glace.php">
        <img src="../img/lave-glace.jpeg" alt="Lave-glace">
      </a>
      <p>Liquide lave-glace performant pour toutes les conditions climatiques.</p>
    </div>

    <div class="produit">
      <h4>Balai d'essuie-glace</h4>
      <a href="balai_essuie_glace.php">
        <img src="../img/balai-essuie-glace.jpg" alt="Balai d'essuie-glace">
      </a>
      <p>Balais résistants assurant un essuyage silencieux et efficace.</p>
    </div>

  </div>
</div>

<div class="produits-groupe">
  <h2>Pneus et Équipements Roue</h2>
  <div class="produits">

    <div class="produit">
      <h4>Pneumatiques</h4>
      <a href="pneu.php">
        <img src="../img/pneumatiques.jpeg" alt="Pneumatiques">
      </a>
      <p>Pneus de haute qualité pour une meilleure adhérence et sécurité sur route.</p>
    </div>

    <div class="produit">
      <h4>Chaînes-neiges et Équipements Roue</h4>
      <a href="chaines_neiges_equipements_roue.php">
        <img src="../img/chaines-neige.jpeg" alt="Chaînes-neiges et équipements">
      </a>
      <p>Équipements indispensables pour rouler en toute sécurité durant l’hiver.</p>
    </div>

    <div class="produit">
      <h4>Outils pneu</h4>
      <a href="outils_pneu.php">
        <img src="../img/outils-pneu.jpeg" alt="Outils pneu">
      </a>
      <p>Outils pratiques pour le montage, la réparation ou le gonflage de vos pneus.</p>
    </div>

  </div>
</div>

<div class="produits-groupe">
  <h2>Chauffage et Climatisation</h2>
  <div class="produits">

    <div class="produit">
      <h4>Climatisation</h4>
      <a href="climatisation.php">
        <img src="../img/climatisation.jpeg" alt="Climatisation">
      </a>
      <p>Systèmes de climatisation pour un confort optimal pendant les journées chaudes.</p>
    </div>

    <div class="produit">
      <h4>Chauffage et Ventilation</h4>
      <a href="chauffage_et_ventilation.php">
        <img src="../img/chauffage.avif" alt="Chauffage et ventilation">
      </a>
      <p>Solutions efficaces pour chauffer l’habitacle et assurer une bonne circulation de l’air.</p>
    </div>

  </div>
</div>
<div class="produits-groupe">
  <h2>Démarrage électrique</h2>
  <div class="produits">

    

    <div class="produit">
      <h4>Alternateurs</h4>
      <a href="alternateurs.php">
        <img src="../img/alternateur.webp" alt="Alternateurs">
      </a>
      <p>Alternateurs haute performance pour une recharge efficace de la batterie.</p>
    </div>

    <div class="produit">
      <h4>Démarreurs</h4>
      <a href="demarreurs.php">
        <img src="../img/demarreur.jpeg" alt="Démarreurs">
      </a>
      <p>Démarreurs robustes pour un démarrage fiable du moteur dans toutes les conditions.</p>
    </div>

    <div class="produit">
      <h4>Outils Batteries</h4>
      <a href="outils_batteries.php">
        <img src="../img/outil-batterie.jpg" alt="Outils Batteries">
      </a>
      <p>Outils pratiques pour l’installation, le test et l’entretien des batteries.</p>
    </div>

  </div>
</div>


  </section>
  <p id="noResultMessage" style="display:none; text-align:center; color: gray; margin-top: 20px;">
    Aucun produit trouvé.
  </p>


  <footer>
    <div class="footer-content">
      <div class="footer-section apropos">
        <h3>À propos de nous</h3>
        <p>
          YRY est une entreprise spécialisée dans la vente de pièces détachées automobiles de haute qualité.
          Nous nous engageons à offrir des produits fiables à des prix compétitifs pour garantir la satisfaction de nos clients.
        </p>
      </div>
  
      <div class="footer-section contact">
        <h3>Contactez-nous</h3>
        <p><strong>Email :</strong> contact@yry-auto.com</p>
        <p><strong>Téléphone :</strong> +213 0790314081  +213</p>
        <p><strong>Adresse :</strong> Zone industrielle, Alger, Algérie</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 YRY. Tous droits réservés.</p>
    </div>
  </footer>
  

  <div id="loginModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2 id="modalTitle">Connexion</h2>
      <div id="loginSignupToggle">
        <button id="loginTab" class="active">Se connecter</button>
        <button id="signupTab">S'inscrire</button>
      </div>

      <form id="loginForm" class="form-content">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Se connecter</button>
      </form>

      <form id="signupForm" class="form-content" style="display: none;">
        <label for="newUsername">Nom d'utilisateur:</label>
        <input type="text" id="newUsername" name="newUsername" required><br><br>
        <label for="newPassword">Mot de passe:</label>
        <input type="password" id="newPassword" name="newPassword" required><br><br>
        <button type="submit">S'inscrire</button>
      </form>
    </div>
  </div>

  <script src="../js/home.js"></script>
</body>
</html>
