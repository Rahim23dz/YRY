<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../html/login.php");
    exit();
}

$username = $_SESSION['username'];

// جلب بيانات المستخدم من جدول users
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// إذا لم يُوجد المستخدم في users، حاول جلبه من vendeurs
if (!$user) {
    $stmt_vendeur = $conn->prepare("SELECT * FROM vendeurs WHERE username = ?");
    $stmt_vendeur->bind_param("s", $username);
    $stmt_vendeur->execute();
    $vendeur_result = $stmt_vendeur->get_result();
    $vendeur = $vendeur_result->fetch_assoc();

    if (!$vendeur) {
        echo "Utilisateur introuvable.";
        exit();
    } else {
        $user = $vendeur;
        $isVendeur = true;
    }
} else {
    $isVendeur = false;
}

// تحديد صورة الملف الشخصي
$default_image = '../uploads/profil/default.jpg';
$profile_image_path = $default_image;

if (!empty($user['profile_image'])) {
    $possible_path = '../uploads/profil/' . $user['profile_image'];
    if (file_exists($possible_path)) {
        $profile_image_path = $possible_path;
    }
}

// إحصائيات للبائع
$stats = [];
if ($isVendeur) {
    // عدد المنتجات
    $stmt_products = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE id_vendeur = ?");
    $stmt_products->bind_param("s", $username);
    $stmt_products->execute();
    $stats['products'] = $stmt_products->get_result()->fetch_assoc()['count'];

    // عدد الطلبات
    $stmt_orders = $conn->prepare("SELECT COUNT(DISTINCT id_commande) as count FROM details_commande ci 
                                   JOIN products p ON p.id_product = p.id_product 
                                   WHERE id_vendeur= ?");
    $stmt_orders->bind_param("s", $username);
    $stmt_orders->execute();
    $stats['orders'] = $stmt_orders->get_result()->fetch_assoc()['count'];

    // إجمالي المبيعات
    $stmt_sales = $conn->prepare("SELECT COALESCE(SUM(ci.quantite * ci.	total), 0) as total 
                                  FROM details_commande ci 
                                  JOIN products p ON p.id_product = p.id_product 
                                  WHERE id_vendeur= ?");
    $stmt_sales->bind_param("s", $username);
    $stmt_sales->execute();
    $stats['sales'] = $stmt_sales->get_result()->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - YRY</title>
    <link rel="stylesheet" href="../css/profil.css">
</head>
<body>
    <!-- Header identique aux autres pages -->
    <header>
        <div class="header-container">
            <a href="../html/home.php" class="logo">
                <img src="../img/logo.jpg" alt="YRY Logo" style="width: 40px; height: 40px; border-radius: 50%;">
                YRY
            </a>
            
            <div class="search-container">
                <input type="text" placeholder="Rechercher des pièces auto...">
            </div>
            
            <div class="header-actions">
              
               <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendeur'): ?>
    <a href="../html/panier.php" class="header-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
        </svg>
        Panier
    </a>
<?php endif; ?>
               
                <a href="logout.php" class="header-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Navigation -->


<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendeur'): ?>
    <!-- Navigation pour Vendeur -->
    <div class="nav-container">
        <div class="nav-links">
            <a href="../html/home.php">🏠 Accueil</a>
            <a href="../html/vondeur.php">➕ Ajouter Produit</a>
            <a href="../php/mes_commandes_vendeur.php">📦 Mes Commandes</a>
            <a href="../html/mes_produits.php">🛍️ Mes Produits</a>
        </div>
    </div>
<?php else: ?>
    <!-- Navigation pour Client ou non connecté -->
    <div class="nav-container">
        <div class="nav-links">
            <a href="../html/home.php">🏠 Accueil</a>
            <a href="frein.php">🔧 Freinage</a>
            <a href="amortisseur.php">⚙️ Suspension</a>
            <a href="transmission.php">🔄 Transmission</a>
            <a href="capteur.php">📡 Capteurs</a>
            <a href="outils.php">🛠️ Outils</a>
        </div>
    </div>
<?php endif; ?>


    <div class="container">
        <aside class="sidebar">
            <h3>Mon Profil</h3>
            <nav>
                <ul><?php if ($isVendeur): ?>
                    <li><a href="profil.php" style="background: var(--primary); color: var(--white);">👤 Voir le profil</a></li>
                    <li><a href="../html/modifier_profil.php">✏️ Modifier le profil</a></li>
                    <li><a href="../php/mes_commandes_vendeur.php">📦 Mes commandes</a></li>
                        <li><a href="../html/mes_produits.php">🛍️ Mes produits</a></li>
                        <li><a href="../html/vendeur_dashboard.php">📊 Dashboard</a></li>
                         <li><a href="logout.php" class="logout-btn">🚪 Déconnexion</a></li>
                    <?php endif; ?>
                    <li><a href="profil.php" style="background: var(--primary); color: var(--white);">👤 Voir le profil</a></li>
                    <li><a href="../html/modifier_profil.php">✏️ Modifier le profil</a></li>
                    <li><a href="../php/mes_commandes.php">📦 Mes commandes</a></li>
                    <li><a href="logout.php" class="logout-btn">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <div class="profil-section">
                <h2>👤 Mon Profil</h2>
                
                <!-- Carte de profil moderne -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="<?= htmlspecialchars($profile_image_path) ?>" alt="Photo de profil">
                            
                        </div>
                        <div class="profile-info">
                            <h3><?= htmlspecialchars($user['username']) ?></h3>
                            <div class="profile-badge">
                                <?= $isVendeur ? '🏪 Vendeur' : '👤 Client' ?>
                            </div>
                            <p class="profile-description">
                                <?= $isVendeur ? 'Membre vendeur de la plateforme YRY' : 'Membre client de la plateforme YRY' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques pour vendeurs -->
              

                <!-- Informations détaillées -->
                <div class="info-section">
                    <h3>📋 Informations personnelles</h3>
                    <div class="profil-info">
                        <div class="info-item">
                            <div class="info-icon">👤</div>
                            <div class="info-content">
                                <strong>Nom d'utilisateur</strong>
                                <span><?= htmlspecialchars($user['username']) ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">📧</div>
                            <div class="info-content">
                                <strong>Email</strong>
                                <span><?= htmlspecialchars($user['email']) ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">📱</div>
                            <div class="info-content">
                                <strong>Téléphone</strong>
                                <span><?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Non renseigné' ?></span>
                            </div>
                        </div>

                        

                        <?php if ($isVendeur): ?>
                        <div class="info-item">
                            <div class="info-icon">🏪</div>
                            <div class="info-content">
                                <strong>Nom du magasin</strong>
                                <span><?= !empty($user['store_name']) ? htmlspecialchars($user['store_name']) : 'Non renseigné' ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <strong>Adresse du magasin</strong>
                                <span><?= !empty($user['store_address']) ? htmlspecialchars($user['store_address']) : 'Non renseignée' ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="action-buttons">
                    <a href="../html/modifier_profil.php" class="btn-modifier">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        Modifier le profil
                    </a>
                    
                    <?php if ($isVendeur): ?>
                    <a href="../html/mes_produits.php" class="btn-modifier btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v10z"/>
                        </svg>
                        Gérer mes produits
                    </a>
                    
                   
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <footer style="background: var(--primary); color: var(--white); text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; 2025 YRY - Pièces Auto. Tous droits réservés.</p>
    </footer>
</body>
</html>