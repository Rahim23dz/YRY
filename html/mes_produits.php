<?php
session_start();
include_once('../php/db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'vendeur') {
     $_SESSION['error_message'] = "⛔ يجب أن تسجّل الدخول كبائع للوصول إلى هذه الصفحة.";
    header('Location: login.php');

    exit();
}

$username = $_SESSION['username'];

// حذف منتج
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_sql = "DELETE FROM products WHERE id_product = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if (!$delete_stmt) {
        echo "Erreur de préparation de la requête de suppression: " . $conn->error;
        exit();
    }
    $delete_stmt->bind_param("i", $delete_id);
    $delete_stmt->execute();
    header("Location: mes_produits.php");
    exit();
}

// تعديل منتج
if (isset($_POST['edit_id'])) {
    $edit_id = (int) $_POST['edit_id'];
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);

    if (!isset($_POST['categorie'])) {
        echo "خطأ: لم يتم إرسال الفئة.";
        exit();
    }
    $categorie = (int) $_POST['categorie'];
    $prix = (float) $_POST['prix'];
    $image = $_FILES['image'];

    // تحقق من وجود الفئة في جدول categories
    $check_cat_stmt = $conn->prepare("SELECT COUNT(*) FROM categories WHERE id_categorie = ?");
    $check_cat_stmt->bind_param("i", $categorie);
    $check_cat_stmt->execute();
    $check_cat_stmt->bind_result($count_cat);
    $check_cat_stmt->fetch();
    $check_cat_stmt->close();

    if ($count_cat == 0) {
        echo "خطأ: الفئة المحددة غير موجودة في قاعدة البيانات.";
        exit();
    }

    // معالجة الصورة
    if ($image['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            echo "خطأ: نوع الملف غير مسموح.";
            exit();
        }
        $image_name = uniqid() . '_' . basename($image['name']);
        move_uploaded_file($image['tmp_name'], '../uploads/products/' . $image_name);
    } else {
        $image_name = $_POST['current_image'];
    }

    $update_sql = "UPDATE products SET nom = ?, description = ?, categorie = ?, prix = ?, image = ? WHERE id_product = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        echo "Erreur de préparation de la requête de mise à jour: " . $conn->error;
        exit();
    }

    $update_stmt->bind_param("ssidsi", $nom, $description, $categorie, $prix, $image_name, $edit_id);
    $update_stmt->execute();

    header("Location: mes_produits.php");
    exit();
}

// جلب المنتجات الخاصة بالبائع مع اسم الفئة
$sql = "SELECT p.*, c.nom_categorie AS nom_categorie 
        FROM products p 
        JOIN categories c ON p.categorie = c.id_categorie 
        WHERE p.id_vendeur = (SELECT id_vendeur FROM vendeurs WHERE username = ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Erreur de préparation de la requête produits: " . $conn->error;
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Produits - YRY</title>
    <link rel="stylesheet" href="../css/mes_produits.css">
</head>
<body>

    <script>
    // إذا كان الـ URL فيه edit_id نطلع المستخدم مباشرة للفورم
    window.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.querySelector('.form-container');
        if (formContainer) {
            formContainer.scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.querySelector('.form-container');
        if (formContainer) {
            const targetY = formContainer.getBoundingClientRect().top + window.scrollY;
            const startY = window.scrollY;
            const distance = targetY - startY;
            const duration = 700; // المدة بالميلي ثانية (هنا 0.5 ثانية)
            let startTime = null;

            function scrollStep(timestamp) {
                if (!startTime) startTime = timestamp;
                const timeElapsed = timestamp - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                window.scrollTo(0, startY + distance * progress);

                if (progress < 1) {
                    window.requestAnimationFrame(scrollStep);
                }
            }

            window.requestAnimationFrame(scrollStep);
        }
    });
</script>


    <!-- ===== HEADER MODERNE IDENTIQUE AUX AUTRES PAGES ===== -->
    <header>
        <div class="header-top">
            <div class="header-left">
                <img src="../img/logo.jpg" alt="Logo YRY">
                <h1>YRY</h1>
            </div>
            
            <div class="search-box">
                <input type="text" placeholder="Rechercher des pièces auto...">
                <span class="search-icon">🔍</span>
            </div>
            
            <div class="header-actions">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="../php/profil.php" class="icon-btn">
                        <div class="icon">👤</div>
                        <div class="label">Profil</div>
                    </a>
                    
                    </a>
                    <a href="../php/mes_commandes.php" class="icon-btn">
                        <div class="icon">📦</div>
                        <div class="label">Commandes</div>
                    </a>
                    <a href="../php/logout.php" class="btn-blue">Déconnexion</a>
                <?php else: ?>
                    <a href="../login_signup.php" class="btn-blue">Connexion</a>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendeur'): ?>
                    <a href="../html/vendeur_dashboard.php" class="btn-vendeur">Espace Vendeur</a>
                <?php endif; ?>
            </div>
        </div>
        
        <nav class="header-nav">
            <a href="../html/home.php">Accueil</a>

            <a href="../html/vondeur.php">ajouter produit</a>
            <a href="../php/mes_commandes.php">Mes Commandes</a>
            <a href="../html/mes_produits.php">Mes Produits</a>
            
        </nav>
    </header>

    <main>
        <h2>🛒 Mes Produits</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="produits-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="produit">
                        <img src="../uploads/products/<?= htmlspecialchars($row['image']) ?>" alt="Image Produit">
                        <div class="info">
                            <h3><?= htmlspecialchars($row['nom']) ?></h3>
                            <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
                            <p><strong>Catégorie:</strong> <?= htmlspecialchars($row['nom_categorie']) ?></p>
                            <p><strong>Prix:</strong> <?= htmlspecialchars($row['prix']) ?> DA</p>
                            <div class="actions">
                                <a href="mes_produits.php?edit_id=<?= $row['id_product'] ?>" class="btn-modifier">Modifier</a>
                                <a href="mes_produits.php?delete_id=<?= $row['id_product'] ?>" class="btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <span class="emoji">😕</span>
                <p>Aucun produit ajouté pour le moment.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['edit_id'])): ?>
            <?php
            $edit_id = (int)$_GET['edit_id'];
            $stmt_edit = $conn->prepare("SELECT p.*, c.nom_categorie FROM products p JOIN categories c ON p.categorie = c.id_categorie WHERE p.id_product = ?");
            $stmt_edit->bind_param("i", $edit_id);
            $stmt_edit->execute();
            $edit_result = $stmt_edit->get_result();
            $product = $edit_result->fetch_assoc();
            
            if ($product):
            ?>
                <div class="form-container">
                    <h2>Modifier le Produit</h2>
                    <form method="POST" enctype="multipart/form-data" action="mes_produits.php">
                        <input type="hidden" name="edit_id" value="<?= $product['id_product'] ?>" />

                        <label>Nom:</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($product['nom']) ?>" required />

                        <label>Description:</label>
                        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

                        <label>Catégorie:</label>
                        <p><strong><?= htmlspecialchars($product['nom_categorie']) ?></strong></p>
                        <input type="hidden" name="categorie" value="<?= htmlspecialchars($product['categorie']) ?>" />

                        <label>Prix:</label>
                        <input type="number" step="0.01" name="prix" value="<?= htmlspecialchars($product['prix']) ?>" required />

                        <label>Image:</label>
                        <input type="file" name="image" />
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image']) ?>" />

                        <button type="submit">Mettre à jour</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

   
</body>
</html>

<?php
// Fermeture de la connexion
$conn->close();
?>