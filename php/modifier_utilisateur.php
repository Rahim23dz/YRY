<?php
session_start();
include '../php/db.php';

if (!isset($_GET['id'])) {
    header('Location: gerer_utilisateurs.php');
    exit();
}

$id = intval($_GET['id']);

// جلب بيانات المستخدم
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Utilisateur non trouvé";
    exit();
}

$user = $result->fetch_assoc();

// جلب جميع الولايات لترتيب القائمة
$sqlWilayas = "SELECT id_wilaya, nom_wilaya FROM wilaya ORDER BY nom_wilaya ASC";
$resultWilayas = $conn->query($sqlWilayas);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?: NULL;
    $profile_image = isset($_POST['profile_image']) ? $_POST['profile_image'] : null; // أو قيمة افتراضية مناسبة
    $role = isset($_POST['role']) ? $_POST['role'] : 'client'; // مثلاً 'client' كدور افتراضي

    $id_wilaya = $_POST['id_wilaya'] ?: NULL;

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE users SET username=?, email=?, password=?, phone=?, profile_image=?, role=?, id_wilaya=? WHERE id_user=?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssssii", $username, $email, $password, $phone, $profile_image, $role, $id_wilaya, $id);
    } else {
        $sqlUpdate = "UPDATE users SET username=?, email=?, phone=?, profile_image=?, role=?, id_wilaya=? WHERE id_user=?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("sssssii", $username, $email, $phone, $profile_image, $role, $id_wilaya, $id);
    }

    if ($stmtUpdate->execute()) {
        $message = "Utilisateur modifié avec succès !";
        $messageType = "success";
        // Refresh user data
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "Erreur lors de la modification: " . $conn->error;
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Utilisateur</title>


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
        const username = document.getElementById('username').value;
        if (!confirm(`Êtes-vous sûr de vouloir modifier l'utilisateur "${username}" ?`)) {
            e.preventDefault();
        }
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>
</head>
<body>
<style>
/* ===== CSS MODERNE POUR MODIFIER UTILISATEUR ===== */
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
  animation: shimmer 3s infinite;
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

/* ===== SECTION INFORMATIONS UTILISATEUR ===== */
.user-info-section {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  padding: 30px 40px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.user-info-section::before {
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

.user-info {
  position: relative;
  z-index: 2;
}

.user-id {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 10px;
}

.user-name {
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

.alert.success {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid rgba(76, 175, 80, 0.3);
}

.alert.success::before {
  content: "✅";
  font-size: 1.2rem;
}

.alert.error {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid rgba(244, 67, 54, 0.3);
}

.alert.error::before {
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
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-label .icon {
  font-size: 1rem;
}

.form-input,
.form-select {
  width: 100%;
  padding: 15px;
  border: 2px solid var(--border);
  border-radius: 8px;
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

.form-select {
  cursor: pointer;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
  appearance: none;
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

  .user-info-section {
    padding: 25px 20px;
  }

  .user-name {
    font-size: 1.5rem;
  }

  .form-section {
    padding: 25px 15px;
  }

  .actions-section {
    padding: 20px 15px;
  }
}

/* ===== ANIMATIONS SUPPLÉMENTAIRES ===== */
.form-input:valid {
  border-color: var(--success);
}

.form-input:invalid:not(:placeholder-shown) {
  border-color: var(--danger);
}

/* Auto-hide alert after 5 seconds */
.alert {
  animation: slideInDown 0.5s ease, fadeOut 0.5s ease 4.5s forwards;
}

@keyframes fadeOut {
  to {
    opacity: 0;
    transform: translateY(-20px);
  }
}
</style>
<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Modifier Utilisateur</h1>
        </div>
        <a href="gerer_utilisateurs.php" class="back-link">
            ← Retour à la liste
        </a>
    </div>
</div>

<div class="container">
    <section class="user-info-section">
        <div class="user-info">
            <div class="user-id">ID Utilisateur: #<?php echo htmlspecialchars($user['id_user']); ?></div>
            <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
        </div>
    </section>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <section class="form-section">
       <form method="post" id="userForm">

            <div class="form-grid">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <span class="icon">👤</span>
                        Nom d'utilisateur
                    </label>
                    <input type="text" id="username" name="username" class="form-input" 
                           value="<?php echo htmlspecialchars($user['username']); ?>" 
                           required minlength="3" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <span class="icon">📧</span>
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <span class="icon">🔒</span>
                        Mot de passe
                    </label>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Laisser vide pour ne pas changer" minlength="6">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">
                        <span class="icon">📱</span>
                        Téléphone
                    </label>
                    <input type="tel" id="phone" name="phone" class="form-input" 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                           pattern="[0-9+\-\s()]*">
                </div>

              

                <div class="form-group">
                    <label for="id_wilaya" class="form-label">
                        <span class="icon">🌍</span>
                        Wilaya
                    </label>
                    <select id="id_wilaya" name="id_wilaya" class="form-select">
                        <option value="">-- Sélectionner une wilaya --</option>
                        <?php 
                        // Reset the result pointer
                        $resultWilayas->data_seek(0);
                        while($wilaya = $resultWilayas->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $wilaya['id_wilaya']; ?>" 
                                    <?php if($wilaya['id_wilaya'] == $user['id_wilaya']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($wilaya['nom_wilaya']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </form>
    </section>

    <section class="actions-section">
        <button type="submit" form="userForm" class="btn btn-primary">
            💾 Enregistrer les modifications
        </button>
        <a href="gerer_utilisateurs.php" class="btn btn-secondary">
            ❌ Annuler
        </a>
    </section>
</div>

<script>
// Fix form submission
document.querySelector('form').id = 'userForm';
</script>

</body>
</html>
