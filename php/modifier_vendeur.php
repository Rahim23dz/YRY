<?php
session_start();
include '../php/db.php';

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// التحقق من وجود id_vendeur في الرابط
if (!isset($_GET['id_vendeur'])) {
    die("ID du vendeur manquant.");
}

$id_vendeur = intval($_GET['id_vendeur']);

// عند ارسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $store_name = $conn->real_escape_string($_POST['store_name']);
    $store_address = $conn->real_escape_string($_POST['store_address']);
    $id_wilaya = intval($_POST['id_wilaya']);

    $sql_update = "UPDATE vendeurs SET
                    username = '$username',
                    email = '$email',
                    phone = '$phone',
                    store_name = '$store_name',
                    store_address = '$store_address',
                    id_wilaya = $id_wilaya
                   WHERE id_vendeur = $id_vendeur";

    if ($conn->query($sql_update) === TRUE) {
        $success_message = "Modification réussie !";
    } else {
        $error_message = "Erreur lors de la modification : " . $conn->error;
    }
}

// جلب بيانات البائع لملئ النموذج
$sql = "SELECT * FROM vendeurs WHERE id_vendeur = $id_vendeur";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    die("Vendeur introuvable.");
}

$vendeur = $result->fetch_assoc();

// جلب قائمة الولايات لعرضها في قائمة منسدلة
$sql_wilayas = "SELECT * FROM wilaya ORDER BY nom_wilaya ASC";
$wilayas_result = $conn->query($sql_wilayas);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Vendeur</title>
<style>
/* ===== CSS MODERNE POUR MODIFIER VENDEUR ===== */
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
  padding: 20px 0;
}

/* ===== HEADER MODERNE ===== */
.page-header {
  background: linear-gradient(135deg, var(--white), var(--light-gray));
  padding: 25px 40px;
  box-shadow: var(--shadow);
  border-bottom: 4px solid var(--primary);
  margin-bottom: 30px;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(0, 121, 107, 0.1), transparent);
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
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
  background: linear-gradient(135deg, var(--secondary), var(--primary));
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

/* ===== SECTION INFORMATIONS VENDEUR ===== */
.vendor-info-section {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  padding: 30px 40px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.vendor-info-section::before {
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

.vendor-info {
  position: relative;
  z-index: 2;
}

.vendor-id {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 10px;
}

.vendor-name {
  font-size: 1.8rem;
  font-weight: 700;
}

/* ===== MESSAGES D'ALERTE ===== */
.alert {
  margin: 20px 40px;
  padding: 15px 20px;
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
  border: 1px solid rgba(76, 175, 80, 0.3);
}

.alert-success::before {
  content: "✅";
  font-size: 1.2rem;
}

.alert-error {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid rgba(244, 67, 54, 0.3);
}

.alert-error::before {
  content: "❌";
  font-size: 1.2rem;
}

/* ===== FORMULAIRE ===== */
.form-section {
  padding: 40px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.form-group {
  position: relative;
  animation: fadeInUp 0.6s ease;
  animation-fill-mode: both;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }

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

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 8px;
  font-size: 0.95rem;
}

.form-input,
.form-select {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid var(--border);
  border-radius: 12px;
  font-size: 1rem;
  transition: var(--transition);
  background: var(--white);
  color: var(--text);
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
  transform: translateY(-2px);
}

.form-input:hover,
.form-select:hover {
  border-color: var(--primary);
}

.form-input:valid {
  border-color: var(--success);
}

.form-input:invalid:not(:placeholder-shown) {
  border-color: var(--danger);
}

/* ===== SECTION ACTIONS ===== */
.actions-section {
  background: var(--light-gray);
  padding: 30px 40px;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 15px 30px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  position: relative;
  overflow: hidden;
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
}

.btn-secondary {
  background: linear-gradient(135deg, var(--text-secondary), #757575);
  color: var(--white);
  box-shadow: 0 4px 15px rgba(117, 117, 117, 0.3);
}

.btn-secondary:hover {
  box-shadow: 0 8px 25px rgba(117, 117, 117, 0.4);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .header-title h1 {
    font-size: 1.8rem;
  }

  .vendor-info-section {
    padding: 25px 20px;
  }

  .vendor-name {
    font-size: 1.5rem;
  }

  .form-section {
    padding: 30px 20px;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .actions-section {
    padding: 25px 20px;
    flex-direction: column;
    align-items: stretch;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  body {
    padding: 10px;
  }

  .page-header {
    padding: 20px;
    margin-bottom: 20px;
  }

  .header-title h1 {
    font-size: 1.5rem;
  }

  .form-input,
  .form-select {
    padding: 12px 15px;
  }

  .alert {
    margin: 15px 20px;
    padding: 12px 15px;
  }
}

/* ===== ANIMATIONS SUPPLÉMENTAIRES ===== */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.btn:active {
  transform: translateY(0);
  animation: pulse 0.3s ease;
}

/* ===== SCROLLBAR PERSONNALISÉE ===== */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--light-gray);
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, var(--primary), var(--accent));
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--secondary), var(--primary));
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation progressive des éléments du formulaire
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.animationDelay = `${index * 0.1}s`;
    });

    // Validation en temps réel
    const inputs = document.querySelectorAll('.form-input');
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
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const vendorName = document.querySelector('input[name="username"]').value;
        if (!confirm(`Êtes-vous sûr de vouloir modifier les informations du vendeur "${vendorName}" ?`)) {
            e.preventDefault();
        }
    });

    // Auto-hide des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});
</script>
</head>
<body>

<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Modifier Vendeur</h1>
        </div>
        <a href="gerer_vendeurs.php" class="back-link">
            ← Retour à la liste
        </a>
    </div>
</div>

<div class="container">
    <section class="vendor-info-section">
        <div class="vendor-info">
            <div class="vendor-id">ID Vendeur: #<?php echo htmlspecialchars($vendeur['id_vendeur']); ?></div>
            <div class="vendor-name"><?php echo htmlspecialchars($vendeur['username']); ?></div>
        </div>
    </section>

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

    <section class="form-section">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        👤 Nom d'utilisateur
                    </label>
                    <input type="text" name="username" class="form-input" 
                           value="<?php echo htmlspecialchars($vendeur['username']); ?>" 
                           placeholder="Entrez le nom d'utilisateur" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        📧 Email
                    </label>
                    <input type="email" name="email" class="form-input" 
                           value="<?php echo htmlspecialchars($vendeur['email']); ?>" 
                           placeholder="Entrez l'adresse email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        📱 Téléphone
                    </label>
                    <input type="tel" name="phone" class="form-input" 
                           value="<?php echo htmlspecialchars($vendeur['phone']); ?>" 
                           placeholder="Entrez le numéro de téléphone">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        🏪 Nom du magasin
                    </label>
                    <input type="text" name="store_name" class="form-input" 
                           value="<?php echo htmlspecialchars($vendeur['store_name']); ?>" 
                           placeholder="Entrez le nom du magasin">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        📍 Adresse du magasin
                    </label>
                    <input type="text" name="store_address" class="form-input" 
                           value="<?php echo htmlspecialchars($vendeur['store_address']); ?>" 
                           placeholder="Entrez l'adresse du magasin">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        🌍 Wilaya
                    </label>
                    <select name="id_wilaya" class="form-select">
                        <option value="">-- Sélectionnez une wilaya --</option>
                        <?php while($wilaya = $wilayas_result->fetch_assoc()): ?>
                            <option value="<?php echo $wilaya['id_wilaya']; ?>" 
                                    <?php if ($wilaya['id_wilaya'] == $vendeur['id_wilaya']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($wilaya['nom_wilaya']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </form>
    </section>

    <section class="actions-section">
        <button type="submit" form="vendor-form" class="btn btn-primary">
            💾 Enregistrer les modifications
        </button>
        <a href="gerer_vendeurs.php" class="btn btn-secondary">
            ❌ Annuler
        </a>
    </section>
</div>

<script>
// Ajouter l'ID au formulaire pour la soumission
document.querySelector('form').id = 'vendor-form';
</script>

</body>
</html>
