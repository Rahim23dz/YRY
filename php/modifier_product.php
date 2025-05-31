<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

if (!isset($_GET['id_product'])) {
    die("ID produit manquant.");
}

$id_product = intval($_GET['id_product']);

// عند الضغط على زر حفظ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $conn->real_escape_string($_POST['nom']);
    $description = $conn->real_escape_string($_POST['description']);
    $categorie = intval($_POST['categorie']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    
    // تحديث بيانات المنتج
    $sql_update = "UPDATE products SET 
        nom='$nom',
        description='$description',
        categorie=$categorie,
        prix=$prix,
        stock=$stock
        WHERE id_product=$id_product";
    
    if ($conn->query($sql_update) === TRUE) {
        $success_message = "Produit mis à jour avec succès.";
        // يمكن عمل redirect مثلاً:
        // header("Location: gerer_products.php");
        // exit;
    } else {
        $error_message = "Erreur lors de la mise à jour: " . $conn->error;
    }
}

// جلب بيانات المنتج لعرضها في النموذج
$sql = "SELECT * FROM products WHERE id_product=$id_product";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    die("Produit introuvable.");
}

$product = $result->fetch_assoc();

// جلب جميع التصنيفات (categories) لاختيار التصنيف
$sql_cat = "SELECT * FROM categories ORDER BY nom_categorie ASC";
$result_cat = $conn->query($sql_cat);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Produit - <?php echo htmlspecialchars($product['nom']); ?></title>
<style>
/* ===== CSS MODERNE POUR MODIFIER PRODUIT ===== */
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
  --text-secondary: #666666;
  --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.15);
  --border-radius: 16px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

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
  padding: 20px;
}

/* ===== HEADER MODERNE ===== */
.page-header {
  background: linear-gradient(135deg, var(--white), var(--light-gray));
  padding: 25px 40px;
  box-shadow: var(--shadow);
  border-bottom: 4px solid var(--primary);
  margin-bottom: 30px;
  border-radius: var(--border-radius);
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, transparent 30%, rgba(0, 121, 107, 0.05) 50%, transparent 70%);
  animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
  0%, 100% { transform: translateX(-100%); }
  50% { transform: translateX(100%); }
}

.header-content {
  max-width: 800px;
  margin: 0 auto;
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

.header-title::before {
  content: "✏️";
  font-size: 2rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
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

.back-link {
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
  position: relative;
  overflow: hidden;
}

.back-link::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: var(--transition);
}

.back-link:hover::before {
  left: 100%;
}

.back-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 121, 107, 0.4);
}

/* ===== CONTAINER PRINCIPAL ===== */
.container {
  max-width: 800px;
  margin: 0 auto;
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  overflow: hidden;
  animation: slideInUp 0.6s ease;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ===== MESSAGES D'ALERTE ===== */
.alert {
  padding: 15px 20px;
  margin: 20px 0;
  border-radius: 8px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
  animation: slideInDown 0.5s ease;
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid var(--success);
}

.alert-success::before {
  content: "✅";
  font-size: 1.2rem;
}

.alert-error {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid var(--danger);
}

.alert-error::before {
  content: "❌";
  font-size: 1.2rem;
}

/* ===== SECTION FORMULAIRE ===== */
.form-section {
  padding: 40px;
}

.form-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 30px;
  text-align: center;
  position: relative;
}

.form-title::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 3px;
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 2px;
}

.form-grid {
  display: grid;
  gap: 25px;
}

.form-group {
  position: relative;
}

.form-label {
  display: block;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 8px;
  font-size: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-label::before {
  content: "📝";
  font-size: 1rem;
}

.form-input,
.form-textarea,
.form-select {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid var(--border);
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  background: var(--white);
  transition: var(--transition);
  position: relative;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
  transform: translateY(-2px);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.form-select {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 50px;
}

/* ===== STYLES SPÉCIAUX POUR LES CHAMPS ===== */
.form-group-nom .form-label::before { content: "🏷️"; }
.form-group-description .form-label::before { content: "📄"; }
.form-group-categorie .form-label::before { content: "📂"; }
.form-group-prix .form-label::before { content: "💰"; }
.form-group-stock .form-label::before { content: "📦"; }

.form-input[type="number"] {
  text-align: right;
}

/* ===== SECTION ACTIONS ===== */
.actions-section {
  background: var(--light-gray);
  padding: 30px 40px;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 15px 30px;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  position: relative;
  overflow: hidden;
  min-width: 160px;
  justify-content: center;
}

.btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: var(--transition);
}

.btn:hover::before {
  left: 100%;
}

.btn:hover {
  transform: translateY(-3px);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  box-shadow: 0 4px 15px rgba(0, 121, 107, 0.3);
}

.btn-primary:hover {
  box-shadow: 0 8px 25px rgba(0, 121, 107, 0.4);
  background: linear-gradient(135deg, var(--secondary), var(--primary));
}

.btn-secondary {
  background: linear-gradient(135deg, var(--text-secondary), #757575);
  color: var(--white);
  box-shadow: 0 4px 15px rgba(117, 117, 117, 0.3);
}

.btn-secondary:hover {
  box-shadow: 0 8px 25px rgba(117, 117, 117, 0.4);
  background: linear-gradient(135deg, var(--text), var(--text-secondary));
}

/* ===== INFORMATIONS PRODUIT ===== */
.product-info {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  padding: 25px;
  margin-bottom: 30px;
  border-radius: 12px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.product-info::before {
  content: "";
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  animation: rotate 20s linear infinite;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.product-name {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 10px;
  position: relative;
  z-index: 2;
}

.product-id {
  font-size: 1rem;
  opacity: 0.9;
  position: relative;
  z-index: 2;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  body {
    padding: 10px;
  }

  .page-header {
    padding: 20px;
    margin-bottom: 20px;
  }

  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .header-title h1 {
    font-size: 1.8rem;
  }

  .form-section {
    padding: 30px 20px;
  }

  .actions-section {
    padding: 25px 20px;
    flex-direction: column;
    align-items: center;
  }

  .btn {
    width: 100%;
    max-width: 300px;
  }
}

@media (max-width: 480px) {
  .header-title h1 {
    font-size: 1.5rem;
  }

  .form-section {
    padding: 25px 15px;
  }

  .form-input,
  .form-textarea,
  .form-select {
    padding: 12px 15px;
  }

  .product-name {
    font-size: 1.3rem;
  }
}

/* ===== ANIMATIONS SUPPLÉMENTAIRES ===== */
.form-group {
  animation: fadeInUp 0.6s ease forwards;
  opacity: 0;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ===== EFFETS HOVER AVANCÉS ===== */
.form-input:hover,
.form-textarea:hover,
.form-select:hover {
  border-color: var(--primary);
  box-shadow: 0 2px 10px rgba(0, 121, 107, 0.1);
}

.form-group:hover .form-label {
  color: var(--accent);
  transform: translateX(5px);
}

/* ===== VALIDATION VISUELLE ===== */
.form-input:valid,
.form-textarea:valid,
.form-select:valid {
  border-color: var(--success);
}

.form-input:invalid:not(:placeholder-shown),
.form-textarea:invalid:not(:placeholder-shown),
.form-select:invalid:not(:placeholder-shown) {
  border-color: var(--danger);
}
</style>
</head>
<body>

<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Modifier Produit</h1>
        </div>
        <a href="gerer_products.php" class="back-link">
            ← Retour aux produits
        </a>
    </div>
</div>

<div class="container">
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="product-info">
        <div class="product-name"><?php echo htmlspecialchars($product['nom']); ?></div>
        <div class="product-id">ID Produit: #<?php echo $product['id_product']; ?></div>
    </div>

    <section class="form-section">
        <h2 class="form-title">Informations du Produit</h2>
        
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group form-group-nom">
                    <label class="form-label" for="nom">Nom du produit</label>
                    <input type="text" id="nom" name="nom" class="form-input" 
                           value="<?php echo htmlspecialchars($product['nom']); ?>" 
                           required placeholder="Entrez le nom du produit">
                </div>

                <div class="form-group form-group-description">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="Décrivez le produit en détail"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group form-group-categorie">
                    <label class="form-label" for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-select" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <?php 
                        // Reset the result pointer
                        $result_cat->data_seek(0);
                        while($cat = $result_cat->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $cat['id_categorie']; ?>" 
                                    <?php if ($cat['id_categorie'] == $product['categorie']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($cat['nom_categorie']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group form-group-prix">
                    <label class="form-label" for="prix">Prix (DA)</label>
                    <input type="number" id="prix" name="prix" class="form-input" 
                           step="0.01" min="0"
                           value="<?php echo htmlspecialchars($product['prix']); ?>" 
                           required placeholder="0.00">
                </div>

                <div class="form-group form-group-stock">
                    <label class="form-label" for="stock">Stock disponible</label>
                    <input type="number" id="stock" name="stock" class="form-input" 
                           min="0"
                           value="<?php echo htmlspecialchars($product['stock']); ?>" 
                           required placeholder="0">
                </div>
            </div>
        </form>
    </section>

    <section class="actions-section">
        <button type="submit" form="productForm" class="btn btn-primary">
            💾 Enregistrer les modifications
        </button>
        <a href="gerer_products.php" class="btn btn-secondary">
            ❌ Annuler
        </a>
    </section>
</div>

<script>
// Ajouter l'ID au formulaire pour la soumission
document.querySelector('form').id = 'productForm';

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Animation des éléments
    const elements = document.querySelectorAll('.form-group');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Validation en temps réel
    const inputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.style.borderColor = 'var(--success)';
            } else {
                this.style.borderColor = 'var(--danger)';
            }
        });
    });

    // Confirmation avant soumission
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const confirmation = confirm('Êtes-vous sûr de vouloir enregistrer ces modifications ?');
        if (!confirmation) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
