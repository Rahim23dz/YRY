<?php
session_start();

// تأكد من صلاحية المسؤول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../html/admin_login.php');
    exit();
}

// الاتصال بقاعدة البيانات
include '../php/db.php';

// جلب الإحصائيات
$stats = [];

// عدد المستخدمين (العملاء فقط)
$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'client'");
$stats['users'] = $result ? $result->fetch_assoc()['total'] : 0;

// عدد البائعين
$result = $conn->query("SELECT COUNT(*) AS total FROM vendeurs");
$stats['vendeurs'] = $result ? $result->fetch_assoc()['total'] : 0;

// عدد المنتجات
$result = $conn->query("SELECT COUNT(*) AS total FROM products");
$stats['products'] = $result ? $result->fetch_assoc()['total'] : 0;

// عدد الطلبات
$result = $conn->query("SELECT COUNT(*) AS total FROM commandes");
$stats['commandes'] = $result ? $result->fetch_assoc()['total'] : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - YRY</title>
  <link rel="stylesheet" href="../css/home.css" />
  <link rel="stylesheet" href="../css/admin_dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<header>
  <div class="header-top">
    <div class="header-left">
      <img src="../img/logo.jpg" alt="Logo YRY" />
      <h1>YRY - Administration</h1>
    </div>
    <div class="search-box">
      <input type="text" id="searchBar" placeholder="Rechercher des utilisateurs, vendeurs, produits..." />
      <span class="search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="7" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
      </span>
    </div>
    <div class="header-actions">
      <a href="../html/profil_admin.php" class="btn-header icon-btn">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
            <circle cx="12" cy="7" r="4" />
            <path d="M5.5 21a7.5 7.5 0 0 1 13 0" />
          </svg>
        </span>
        <span class="label">Profil Admin</span>
      </a>
    
      <a href="../php/logout.php" class="btn-header icon-btn">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16,17 21,12 16,7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </span>
        <span class="label">Déconnexion</span>
      </a>
    </div>
  </div>

  <div class="header-bottom">
    <nav class="header-nav">
      <button class="btn-blue" onclick="location.href='../html/admin_dashboard.php'">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;" viewBox="0 0 24 24">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
          <line x1="9" y1="9" x2="15" y2="15" />
          <line x1="15" y1="9" x2="9" y2="15" />
        </svg>
        Tableau de bord
      </button>
      <a href="../php/gerer_utilisateurs.php">Gérer Utilisateurs</a>
      <a href="../php/gerer_vendeurs.php">Gérer Vendeurs</a>
      <a href="../php/gerer_products.php">Gérer Produits</a>
      <a href="../php/gerer_commandes.php">Gérer Commandes</a>
    </nav>
  </div>
</header>

<div class="banner-image admin-banner">
  <div style="position: relative;">
    <img src="../img/admin-banner.jpg" alt="Bannière Admin YRY" style="width: 100%; height: 350px; object-fit: cover;" />
    <div
      class="banner-text"
      style="
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.4);
        padding: 20px;
        border-radius: 10px;
      "
    >
      <h1>Tableau de bord YRY</h1>
      <p>Contrôle total du système : gestion des produits, commandes et vendeurs</p>
    </div>
  </div>
  <h2>Tableau de bord Administrateur</h2>
  <p>Gérez efficacement votre plateforme e-commerce</p>
</div>

<section class="highlights admin-stats">
  <div class="highlight-item stats-card">
    <i class="fa-solid fa-users"></i>
    <p><strong><?php echo $stats['users']; ?> Utilisateurs</strong><br /><span>clients actifs</span></p>
  </div>
  <div class="highlight-item stats-card">
    <i class="fa-solid fa-store"></i>
    <p><strong><?php echo $stats['vendeurs']; ?> Vendeurs</strong><br /><span>partenaires</span></p>
  </div>
  <div class="highlight-item stats-card">
    <i class="fa-solid fa-box"></i>
    <p><strong><?php echo $stats['products']; ?> Produits</strong><br /><span>en catalogue</span></p>
  </div>
  <div class="highlight-item stats-card">
    <i class="fa-solid fa-shopping-cart"></i>
    <p><strong><?php echo $stats['commandes']; ?> Commandes</strong><br /><span>traitées</span></p>
  </div>
</section>

<section id="admin-dashboard" class="dashboard-main">
  <div class="produits-groupe admin-actions">
    <h2>Actions Rapides</h2>
    <div class="produits admin-quick-actions">
      <div class="produit admin-action-card">
        <h4>Gérer Utilisateurs</h4>
        <a href="../php/gerer_utilisateurs.php">
          <i class="fa-solid fa-users-gear admin-icon"></i>
        </a>
        <p>Ajouter, modifier ou supprimer des comptes utilisateurs</p>
        <a href="../php/gerer_utilisateurs.php" class="btn btn-primary">Accéder</a>
      </div>

      <div class="produit admin-action-card">
        <h4>Gérer Vendeurs</h4>
        <a href="../php/gerer_vendeurs.php">
          <i class="fa-solid fa-store admin-icon"></i>
        </a>
        <p>Contrôler les vendeurs et leurs boutiques</p>
        <a href="../php/gerer_vendeurs.php" class="btn btn-primary">Accéder</a>
      </div>

      <div class="produit admin-action-card">
        <h4>Gérer Produits</h4>
        <a href="../php/gerer_products.php">
          <i class="fa-solid fa-box admin-icon"></i>
        </a>
        <p>Ajouter, modifier ou supprimer des produits</p>
        <a href="../php/gerer_products.php" class="btn btn-primary">Accéder</a>
      </div>

      <div class="produit admin-action-card">
        <h4>Gérer Commandes</h4>
        <a href="../php/gerer_commandes.php">
          <i class="fa-solid fa-shopping-cart admin-icon"></i>
        </a>
        <p>Suivre et gérer les commandes des clients</p>
        <a href="../php/gerer_commandes.php" class="btn btn-primary">Accéder</a>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>© 2025 YRY - Tous droits réservés</p>
</footer>

<script src="../js/admin_dashboard.js"></script>
<script src="../js/home.js"></script>
</body>
</html>
