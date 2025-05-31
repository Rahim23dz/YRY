<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['username'])) {
    echo "Aucun utilisateur connecté.";
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

// تحديد صورة الملف الشخصي مع التحقق من وجود الصورة فعلاً
$default_image = '../uploads/profil/default.jpg';
$profile_image_path = $default_image;

if (!empty($user['profile_image'])) {
    $possible_path = '../uploads/profil/' . $user['profile_image'];
    if (file_exists($possible_path)) {
        $profile_image_path = $possible_path;
    }
}

$msg = '';

// معالجة تعديل الملف الشخصي عند ارسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    // معالجة رفع الصورة
    $profile_image_name = $user['profile_image'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profil/';
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $profile_image_name = uniqid() . '_' . $_FILES['photo']['name'];
        $upload_path = $upload_dir . $profile_image_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            // حذف الصورة القديمة إذا لم تكن الافتراضية
            if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.jpg') {
                $old_image_path = $upload_dir . $user['profile_image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }

    if ($isVendeur) {
        $store_name = $_POST['store_name'] ?? '';
        $store_address = $_POST['store_address'] ?? '';

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE vendeurs SET username=?, email=?, password=?, phone=?, profile_image=?, store_name=?, store_address=? WHERE username=?");
            $stmt_update->bind_param("ssssssss", $new_username, $email, $hashed_password, $phone, $profile_image_name, $store_name, $store_address, $username);
        } else {
            $stmt_update = $conn->prepare("UPDATE vendeurs SET username=?, email=?, phone=?, profile_image=?, store_name=?, store_address=? WHERE username=?");
            $stmt_update->bind_param("sssssss", $new_username, $email, $phone, $profile_image_name, $store_name, $store_address, $username);
        }
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE users SET username=?, email=?, password=?, phone=?, profile_image=? WHERE username=?");
            $stmt_update->bind_param("ssssss", $new_username, $email, $hashed_password, $phone, $profile_image_name, $username);
        } else {
            $stmt_update = $conn->prepare("UPDATE users SET username=?, email=?, phone=?, profile_image=? WHERE username=?");
            $stmt_update->bind_param("sssss", $new_username, $email, $phone, $profile_image_name, $username);
        }
    }

    if ($stmt_update->execute()) {
        $_SESSION['username'] = $new_username;
        $msg = "<div class='alert alert-success'>Profil mis à jour avec succès !</div>";
        // تحديث بيانات المستخدم
        $user['username'] = $new_username;
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['profile_image'] = $profile_image_name;
        if ($isVendeur) {
            $user['store_name'] = $store_name;
            $user['store_address'] = $store_address;
        }
        $profile_image_path = '../uploads/profil/' . $profile_image_name;
    } else {
        $msg = "<div class='alert alert-error'>Erreur lors de la mise à jour.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil - YRY</title>
    <link rel="stylesheet" href="../css/profil.css">
    <style>
        /* Styles spécifiques pour la modification */
        .form-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px dashed var(--primary);
            border-radius: var(--border-radius);
            background: rgba(0, 121, 107, 0.05);
            transition: var(--transition);
        }

        .file-input-display:hover {
            background: rgba(0, 121, 107, 0.1);
            border-color: var(--primary-dark);
        }

        .current-image {
            text-align: center;
            margin: 1rem 0;
        }

        .current-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            box-shadow: var(--shadow);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-dark), #00251a);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-back {
            display: inline-block;
            background: var(--secondary);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2rem;
        }

        .btn-back:hover {
            background: var(--secondary-dark);
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }

/* Améliorations pour la page modifier profil */
.profile-preview {
    background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    color: var(--white);
    text-align: center;
}

.preview-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.3);
    margin-bottom: 1rem;
}

.form-section {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-light);
}

.form-section h3 {
    color: var(--primary);
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.password-strength {
    margin-top: 0.5rem;
    padding: 0.5rem;
    border-radius: var(--border-radius);
    font-size: 0.85rem;
    font-weight: 500;
}

.strength-weak { background: #ffebee; color: #c62828; }
.strength-medium { background: #fff3e0; color: #ef6c00; }
.strength-strong { background: #e8f5e8; color: #2e7d32; }

.file-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: var(--border-radius);
    border: 2px dashed var(--border);
}

.preview-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary);
}

.file-info {
    flex: 1;
}

.file-name {
    font-weight: 600;
    color: var(--text-dark);
}

.file-size {
    font-size: 0.85rem;
    color: var(--text-light);
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: var(--border);
    border-radius: 2px;
    overflow: hidden;
    margin-top: 0.5rem;
}

.progress-fill {
    height: 100%;
    background: var(--primary);
    transition: width 0.3s ease;
}

.btn-group {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-cancel {
    background: var(--border);
    color: var(--text-dark);
    border: 2px solid var(--border);
}

.btn-cancel:hover {
    background: #dee2e6;
    border-color: #adb5bd;
}

.success-message {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    padding: 1rem;
    border-radius: var(--border-radius);
    border-left: 4px solid #28a745;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.error-message {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    padding: 1rem;
    border-radius: var(--border-radius);
    border-left: 4px solid #dc3545;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
    </style>
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
            <a href="../php/mes_commandes.php">📦 Mes Commandes</a>
            <a href="../html/mes_produits.php">🛍️ Mes Produits</a>
        </div>
    </div>
<?php else: ?>
    <!-- Navigation pour Client ou non connecté -->
    <div class="nav-container">
        <div class="nav-links">
            <a href="home.php">🏠 Accueil</a>
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
                <ul>
                    <li><a href="../php/profil.php">👤 Voir le profil</a></li>
                    <li><a href="modifier_profil.php" style="background: var(--primary); color: var(--white);">✏️ Modifier le profil</a></li>
                    <li><a href="../php/mes_commandes.php">📦 Mes commandes</a></li>
                    <?php if ($isVendeur): ?>
                        <li><a href="mes_produits.php">🛍️ Mes produits</a></li>
                        <li><a href="vendeur_dashboard.php">📊 Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="../php/logout.php" class="logout-btn">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <a href="../php/profil.php" class="btn-back">← Retour au profil</a>
            
            <div class="profil-section">
                <h2>✏️ Modifier mon profil</h2>
                
                <?php echo $msg; ?>
                
                <div class="profile-preview">
    <img src="<?= htmlspecialchars($profile_image_path) ?>" alt="Aperçu" class="preview-avatar" id="preview-avatar">
    <h3><?= htmlspecialchars($user['username']) ?></h3>
    <p><?= $isVendeur ? '🏪 Vendeur' : '👤 Client' ?></p>
</div>

<form method="POST" enctype="multipart/form-data" id="profile-form">
    <!-- Section Informations personnelles -->
    <div class="form-section">
        <h3>👤 Informations personnelles</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+213 XX XX XX XX XX">
        </div>
    </div>

    <!-- Section Sécurité -->
    <div class="form-section">
        <h3>🔒 Sécurité</h3>
        <div class="form-group">
            <label for="password">Nouveau mot de passe (optionnel)</label>
            <input type="password" id="password" name="password" placeholder="Laisser vide pour ne pas changer">
            <div id="password-strength" class="password-strength" style="display: none;"></div>
        </div>
    </div>

    <?php if ($isVendeur): ?>
    <!-- Section Magasin -->
    <div class="form-section">
        <h3>🏪 Informations du magasin</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="store_name">Nom du magasin</label>
                <input type="text" id="store_name" name="store_name" value="<?= htmlspecialchars($user['store_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="store_address">Adresse du magasin</label>
                <input type="text" id="store_address" name="store_address" value="<?= htmlspecialchars($user['store_address'] ?? '') ?>">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section Photo -->
    <div class="form-section">
        <h3>📷 Photo de profil</h3>
        <div class="form-group">
            <label for="photo">Choisir une nouvelle photo</label>
            <div class="file-input-wrapper">
                <input type="file" id="photo" name="photo" accept="image/*">
                <div class="file-input-display">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                    </svg>
                    <span>Cliquez pour choisir une nouvelle photo</span>
                </div>
            </div>
            
            <div id="file-preview" class="file-preview" style="display: none;">
                <img id="preview-img" class="preview-image" src="/placeholder.svg" alt="Aperçu">
                <div class="file-info">
                    <div class="file-name" id="file-name"></div>
                    <div class="file-size" id="file-size"></div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="btn-group">
        <button type="submit" class="btn-submit">
            💾 Enregistrer les modifications
        </button>
        <a href="../php/profil.php" class="btn-submit btn-cancel">
            ❌ Annuler
        </a>
    </div>
</form>
            </div>
        </main>
    </div>

    <footer style="background: var(--primary); color: var(--white); text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; 2025 YRY - Pièces Auto. Tous droits réservés.</p>
    </footer>
<script>
// Validation du mot de passe en temps réel
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthDiv = document.getElementById('password-strength');
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }
    
    strengthDiv.style.display = 'block';
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    if (strength < 2) {
        strengthDiv.className = 'password-strength strength-weak';
        strengthDiv.textContent = '⚠️ Mot de passe faible';
    } else if (strength < 4) {
        strengthDiv.className = 'password-strength strength-medium';
        strengthDiv.textContent = '⚡ Mot de passe moyen';
    } else {
        strengthDiv.className = 'password-strength strength-strong';
        strengthDiv.textContent = '✅ Mot de passe fort';
    }
});

// Aperçu de l'image
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-avatar').src = e.target.result;
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            document.getElementById('file-preview').style.display = 'flex';
            
            // Animation de la barre de progression
            const progressFill = document.getElementById('progress-fill');
            progressFill.style.width = '0%';
            setTimeout(() => {
                progressFill.style.width = '100%';
            }, 100);
        };
        reader.readAsDataURL(file);
    }
});

// Animation du formulaire
document.getElementById('profile-form').addEventListener('submit', function() {
    const submitBtn = document.querySelector('.btn-submit');
    submitBtn.innerHTML = '⏳ Enregistrement...';
    submitBtn.disabled = true;
});
</script>
</body>
</html>