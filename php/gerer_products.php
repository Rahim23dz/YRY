<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// استعلام لجلب المنتجات مع اسم التصنيف واسم البائع
$sql = "SELECT p.id_product, p.nom, p.description, p.prix, p.image, p.stock,
        c.nom_categorie, v.username as vendeur_nom
        FROM products p
        LEFT JOIN categories c ON p.categorie = c.id_categorie
        LEFT JOIN vendeurs v ON p.id_vendeur = v.id_vendeur
        ORDER BY p.nom ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur SQL: " . $conn->error);
}

// Compter le nombre total de produits
$total_products = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gérer Produits - Administration</title>
<style>
/* ===== VARIABLES CSS ===== */
:root {
  --primary: #00796b;
  --secondary: #004d40;
  --accent: #ff6f00;
  --success: #4caf50;
  --warning: #ff9800;
  --danger: #f44336;
  --info: #2196f3;
  --white: #ffffff;
  --light-gray: #f8f9fa;
  --border: #e0e0e0;
  --text: #212121;
  --text-light: #666666;
  --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ===== RESET ET BASE ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  color: var(--text);
  line-height: 1.6;
  min-height: 100vh;
}

/* ===== CONTAINER PRINCIPAL ===== */
.admin-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 30px 20px;
  min-height: 100vh;
}

/* ===== HEADER DE LA PAGE ===== */
.page-header {
  background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
  border-radius: var(--border-radius);
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  border-left: 5px solid var(--primary);
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100%;
  background: linear-gradient(45deg, transparent, rgba(0, 121, 107, 0.1));
  transform: skewX(-15deg);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  position: relative;
  z-index: 2;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 15px;
}

.header-title h1 {
  font-size: 2.2rem;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 700;
  margin: 0;
}

.header-title .icon {
  font-size: 2.5rem;
  animation: bounce 2s infinite;
}

.back-button {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  text-decoration: none;
  border-radius: 25px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(0, 121, 107, 0.3);
}

.back-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 121, 107, 0.4);
  background: linear-gradient(135deg, var(--secondary), var(--primary));
}

/* ===== STATISTIQUES RAPIDES ===== */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--white);
  padding: 25px 20px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  text-align: center;
  position: relative;
  overflow: hidden;
  transition: var(--transition);
  border-top: 4px solid var(--primary);
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: var(--transition);
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

.stat-card:hover::before {
  left: 100%;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 5px;
  display: block;
}

.stat-label {
  color: var(--text-light);
  font-size: 0.9rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ===== SECTION ACTIONS ===== */
.actions-section {
  background: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  margin-bottom: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.search-container {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
  max-width: 400px;
}

.search-input {
  flex: 1;
  padding: 12px 20px;
  border: 2px solid var(--border);
  border-radius: 25px;
  font-size: 1rem;
  transition: var(--transition);
  background: var(--light-gray);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
}

.action-buttons {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  white-space: nowrap;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
}

.btn-success {
  background: linear-gradient(135deg, var(--success), #66bb6a);
  color: var(--white);
}

.btn-export {
  background: linear-gradient(135deg, var(--info), #42a5f5);
  color: var(--white);
}

/* ===== TABLEAU PRINCIPAL ===== */
.table-container {
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  overflow: hidden;
  margin-bottom: 30px;
  animation: fadeInUp 0.6s ease;
}

.table-header {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  padding: 20px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-header h3 {
  font-size: 1.3rem;
  font-weight: 600;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.table-stats {
  font-size: 0.9rem;
  opacity: 0.9;
  background: rgba(255, 255, 255, 0.2);
  padding: 5px 12px;
  border-radius: 15px;
}

.table-wrapper {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

.data-table th {
  background: var(--light-gray);
  padding: 15px 12px;
  text-align: left;
  font-weight: 600;
  color: var(--text);
  border-bottom: 2px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 10;
  white-space: nowrap;
}

.data-table td {
  padding: 15px 12px;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
}

.data-table tr {
  transition: var(--transition);
}

.data-table tr:hover {
  background: rgba(0, 121, 107, 0.05);
  transform: scale(1.01);
}

.data-table tr:nth-child(even) {
  background: rgba(248, 249, 250, 0.5);
}

/* ===== IMAGES PRODUITS ===== */
.product-img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--border);
  transition: var(--transition);
  cursor: pointer;
}

.product-img:hover {
  transform: scale(1.1);
  border-color: var(--primary);
  box-shadow: 0 4px 15px rgba(0, 121, 107, 0.3);
}

/* ===== BADGES ET STATUTS ===== */
.category-badge {
  display: inline-block;
  padding: 6px 12px;
  background: linear-gradient(135deg, var(--info), #42a5f5);
  color: var(--white);
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.price-tag {
  font-weight: 700;
  color: var(--success);
  font-size: 1.1rem;
}

.stock-indicator {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.stock-high {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid rgba(76, 175, 80, 0.3);
}

.stock-medium {
  background: rgba(255, 152, 0, 0.1);
  color: var(--warning);
  border: 1px solid rgba(255, 152, 0, 0.3);
}

.stock-low {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid rgba(244, 67, 54, 0.3);
}

.vendor-name {
  color: var(--primary);
  font-weight: 600;
  text-decoration: none;
  transition: var(--transition);
}

.vendor-name:hover {
  color: var(--accent);
  text-decoration: underline;
}

/* ===== ACTIONS DE LIGNE ===== */
.row-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}

.action-btn {
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 500;
  transition: var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  white-space: nowrap;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.action-edit {
  background: linear-gradient(135deg, var(--info), #42a5f5);
  color: var(--white);
}

.action-delete {
  background: linear-gradient(135deg, var(--danger), #ef5350);
  color: var(--white);
}

.action-view {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
}

/* ===== DESCRIPTION TRONQUÉE ===== */
.description-cell {
  max-width: 200px;
  position: relative;
}

.description-text {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.4;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
  .admin-container {
    padding: 20px 15px;
  }

  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .actions-section {
    flex-direction: column;
    align-items: stretch;
  }

  .search-container {
    max-width: 100%;
  }

  .action-buttons {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .header-title h1 {
    font-size: 1.8rem;
  }

  .stats-section {
    grid-template-columns: repeat(2, 1fr);
  }

  .data-table {
    font-size: 0.85rem;
  }

  .data-table th,
  .data-table td {
    padding: 10px 8px;
  }

  .product-img {
    width: 60px;
    height: 60px;
  }

  .row-actions {
    flex-direction: column;
    gap: 5px;
  }

  .action-btn {
    font-size: 0.8rem;
    padding: 6px 10px;
  }
}

@media (max-width: 480px) {
  .stats-section {
    grid-template-columns: 1fr;
  }

  .header-title h1 {
    font-size: 1.5rem;
  }

  .table-container {
    margin: 0 -10px;
    border-radius: 0;
  }

  .data-table {
    min-width: 800px;
  }

  .btn {
    font-size: 0.85rem;
    padding: 10px 16px;
  }
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
  60% {
    transform: translateY(-5px);
  }
}

@keyframes shimmer {
  0% {
    background-position: -200px 0;
  }
  100% {
    background-position: calc(200px + 100%) 0;
  }
}

.table-container {
  animation: fadeInUp 0.6s ease;
}

.stat-card {
  animation: fadeInUp 0.6s ease;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* ===== SCROLLBAR PERSONNALISÉE ===== */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--light-gray);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--secondary), #ff9100);
}

/* ===== UTILITAIRES ===== */
.text-center { text-align: center; }
.text-right { text-align: right; }
.font-bold { font-weight: 700; }
.text-muted { color: var(--text-light); }

/* ===== ÉTATS DE CHARGEMENT ===== */
.loading {
  opacity: 0.6;
  pointer-events: none;
}

.loading::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmer 1.5s infinite;
}
</style>
</head>
<body>

<div class="admin-container">
  <!-- Header de la page -->
  <div class="page-header">
    <div class="header-content">
      <div class="header-title">
        <span class="icon">📦</span>
        <h1>Gestion des Produits</h1>
      </div>
      <a href="../html/admin_dashboard.php" class="back-button">
        ← Retour au Dashboard
      </a>
    </div>
  </div>

  <!-- Statistiques rapides -->
  <div class="stats-section">
    <div class="stat-card">
      <span class="stat-number"><?php echo $total_products; ?></span>
      <span class="stat-label">Total Produits</span>
    </div>
    <div class="stat-card">
      <span class="stat-number">
        <?php 
        $result->data_seek(0);
        $stock_total = 0;
        while($row = $result->fetch_assoc()) {
          $stock_total += $row['stock'] ?? 0;
        }
        echo number_format($stock_total);
        ?>
      </span>
      <span class="stat-label">Stock Total</span>
    </div>
    <div class="stat-card">
      <span class="stat-number">
        <?php 
        $categories_result = $conn->query("SELECT COUNT(DISTINCT categorie) as total FROM products WHERE categorie IS NOT NULL");
        echo $categories_result->fetch_assoc()['total'];
        ?>
      </span>
      <span class="stat-label">Catégories</span>
    </div>
    <div class="stat-card">
      <span class="stat-number">
        <?php 
        $vendeurs_result = $conn->query("SELECT COUNT(DISTINCT id_vendeur) as total FROM products WHERE id_vendeur IS NOT NULL");
        echo $vendeurs_result->fetch_assoc()['total'];
        ?>
      </span>
      <span class="stat-label">Vendeurs Actifs</span>
    </div>
  </div>

  <!-- Section actions -->
 

  <!-- Tableau des produits -->
  <div class="table-container">
    <div class="table-header">
      <h3>📋 Liste des Produits</h3>
      <span class="table-stats"><?php echo $total_products; ?> produits</span>
    </div>
    
    <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Vendeur</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $result->data_seek(0);
          while($product = $result->fetch_assoc()): 
            $stock = $product['stock'] ?? 0;
            $stock_class = $stock > 20 ? 'stock-high' : ($stock > 5 ? 'stock-medium' : 'stock-low');
            $stock_icon = $stock > 20 ? '✅' : ($stock > 5 ? '⚠️' : '❌');
          ?>
          <tr>
            <td><strong>#<?php echo htmlspecialchars($product['id_product']); ?></strong></td>
            <td>
              <?php if (!empty($product['image'])): ?>
                <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="Image produit" class="product-img" 
                     title="<?php echo htmlspecialchars($product['nom']); ?>" />
              <?php else: ?>
                <div style="width: 80px; height: 80px; background: var(--light-gray); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                  📷
                </div>
              <?php endif; ?>
            </td>
            <td>
              <strong><?php echo htmlspecialchars($product['nom']); ?></strong>
            </td>
            <td class="description-cell">
              <div class="description-text" title="<?php echo htmlspecialchars($product['description']); ?>">
                <?php echo htmlspecialchars($product['description']); ?>
              </div>
            </td>
            <td>
              <?php if (!empty($product['nom_categorie'])): ?>
                <span class="category-badge"><?php echo htmlspecialchars($product['nom_categorie']); ?></span>
              <?php else: ?>
                <span class="text-muted">Non définie</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="price-tag"><?php echo number_format($product['prix'], 2, ',', ' '); ?> DA</span>
            </td>
            <td>
              <span class="stock-indicator <?php echo $stock_class; ?>">
                <?php echo $stock_icon; ?> <?php echo $stock; ?>
              </span>
            </td>
            <td>
              <?php if (!empty($product['vendeur_nom'])): ?>
                <a href="voir_vendeur.php?id=<?php echo $product['id_vendeur'] ?? ''; ?>" class="vendor-name">
                  👤 <?php echo htmlspecialchars($product['vendeur_nom']); ?>
                </a>
              <?php else: ?>
                <span class="text-muted">Non assigné</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="row-actions">
                <a href="modifier_product.php?id_product=<?php echo $product['id_product']; ?>" 
                   class="action-btn action-edit" title="Modifier">
                  ✏️ Modifier
                </a>
                <form method="post" action="supprimer_product.php" 
                      onsubmit="return confirm('Voulez-vous vraiment supprimer ce produit ?');" 
                      style="display:inline;">
                  <input type="hidden" name="id_product" value="<?php echo $product['id_product']; ?>">
                  <button type="submit" class="action-btn action-delete" title="Supprimer">
                    🗑️ Supprimer
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
// Recherche en temps réel
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Confirmation de suppression améliorée
document.querySelectorAll('.action-delete').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productName = this.closest('tr').querySelector('strong').textContent;
        if (confirm(`Êtes-vous sûr de vouloir supprimer le produit "${productName}" ?\n\nCette action est irréversible.`)) {
            this.closest('form').submit();
        }
    });
});
</script>

</body>
</html>
