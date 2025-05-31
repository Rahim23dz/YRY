<?php
session_start();
include '../php/db.php'; // تأكد من مسار الاتصال بقاعدة البيانات

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}

// تحديث حالة الطلب إذا تم إرسال نموذج التعديل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_commande = intval($_POST['id_commande']);
    $nouveau_statut = $conn->real_escape_string($_POST['statut']);

    $sql_update = "UPDATE commandes SET statut = '$nouveau_statut' WHERE id_commande = $id_commande";
    if ($conn->query($sql_update) === TRUE) {
        $message = "Statut mis à jour avec succès.";
        $message_type = "success";
    } else {
        $message = "Erreur lors de la mise à jour: " . $conn->error;
        $message_type = "error";
    }
}

// حذف طلب إذا تم إرسال طلب الحذف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $id_commande = intval($_POST['id_commande']);

    $sql_delete = "DELETE FROM commandes WHERE id_commande = $id_commande";
    if ($conn->query($sql_delete) === TRUE) {
        $message = "Commande supprimée avec succès.";
        $message_type = "success";
    } else {
        $message = "Erreur lors de la suppression: " . $conn->error;
        $message_type = "error";
    }
}

// جلب الطلبات مع اسم المستخدم
$sql = "SELECT c.id_commande, c.total, c.date_commande, c.statut, u.username 
        FROM commandes c
        LEFT JOIN users u ON c.id_user = u.id_user
        ORDER BY c.date_commande DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur SQL: " . $conn->error);
}

// حساب الإحصائيات
$stats_sql = "SELECT 
    COUNT(*) as total_commandes,
    SUM(total) as total_revenus,
    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'confirmé' THEN 1 ELSE 0 END) as confirme,
    SUM(CASE WHEN statut = 'livré' THEN 1 ELSE 0 END) as livre,
    SUM(CASE WHEN statut = 'annulé' THEN 1 ELSE 0 END) as annule
    FROM commandes";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gérer les Commandes</title>
<style>
/* ===== CSS MODERNE POUR GESTION DES COMMANDES ===== */
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
  padding: 30px 40px;
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
  max-width: 1200px;
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
  content: "📋";
  font-size: 2.5rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

.header-title h1 {
  font-size: 2.5rem;
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
  padding: 12px 24px;
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
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 121, 107, 0.4);
}

/* ===== CONTAINER PRINCIPAL ===== */
.container {
  max-width: 1200px;
  margin: 0 auto;
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

/* ===== STATISTIQUES ===== */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  text-align: center;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  border-left: 4px solid var(--primary);
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, transparent 0%, rgba(0, 121, 107, 0.05) 100%);
  opacity: 0;
  transition: var(--transition);
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card:nth-child(1) { border-left-color: var(--info); }
.stat-card:nth-child(2) { border-left-color: var(--success); }
.stat-card:nth-child(3) { border-left-color: var(--warning); }
.stat-card:nth-child(4) { border-left-color: var(--danger); }

.stat-icon {
  font-size: 2.5rem;
  margin-bottom: 15px;
  display: block;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 5px;
  display: block;
}

.stat-label {
  color: var(--text-secondary);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.9rem;
}

/* ===== BARRE D'ACTIONS ===== */
.actions-bar {
  background: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  margin-bottom: 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.search-container {
  position: relative;
  flex: 1;
  max-width: 400px;
}

.search-input {
  width: 100%;
  padding: 12px 45px 12px 15px;
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

.search-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
  font-size: 1.2rem;
}

.filter-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 10px 20px;
  border: 2px solid var(--border);
  background: var(--white);
  color: var(--text);
  border-radius: 20px;
  cursor: pointer;
  transition: var(--transition);
  font-weight: 600;
  font-size: 0.9rem;
}

.filter-btn:hover,
.filter-btn.active {
  background: var(--primary);
  color: var(--white);
  border-color: var(--primary);
  transform: translateY(-2px);
}

/* ===== MESSAGES ===== */
.message {
  padding: 15px 20px;
  border-radius: 12px;
  margin-bottom: 25px;
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

.message.success {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border: 1px solid rgba(76, 175, 80, 0.3);
}

.message.error {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border: 1px solid rgba(244, 67, 54, 0.3);
}

.message::before {
  font-size: 1.2rem;
}

.message.success::before {
  content: "✅";
}

.message.error::before {
  content: "❌";
}

/* ===== TABLEAU ===== */
.table-container {
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}

.table-header {
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  color: var(--white);
  padding: 20px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-title {
  font-size: 1.3rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}

.table-count {
  background: rgba(255, 255, 255, 0.2);
  padding: 5px 12px;
  border-radius: 15px;
  font-size: 0.9rem;
  font-weight: 600;
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
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  color: var(--text);
  padding: 15px 12px;
  text-align: left;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.85rem;
  border-bottom: 2px solid var(--border);
}

.data-table td {
  padding: 15px 12px;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
}

.data-table tbody tr {
  transition: var(--transition);
}

.data-table tbody tr:nth-child(even) {
  background: rgba(0, 121, 107, 0.02);
}

.data-table tbody tr:hover {
  background: rgba(0, 121, 107, 0.08);
  transform: scale(1.01);
}

/* ===== BADGES DE STATUT ===== */
.status-badge {
  padding: 6px 12px;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: 2px solid;
  display: inline-block;
  transition: var(--transition);
}

.status-badge.en_attente {
  background: rgba(255, 152, 0, 0.1);
  color: var(--warning);
  border-color: var(--warning);
}

.status-badge.confirmé {
  background: rgba(33, 150, 243, 0.1);
  color: var(--info);
  border-color: var(--info);
}

.status-badge.livré {
  background: rgba(76, 175, 80, 0.1);
  color: var(--success);
  border-color: var(--success);
}

.status-badge.annulé {
  background: rgba(244, 67, 54, 0.1);
  color: var(--danger);
  border-color: var(--danger);
}

/* ===== FORMULAIRES ET BOUTONS ===== */
.status-form {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 0;
}

.status-select {
  padding: 8px 12px;
  border: 2px solid var(--border);
  border-radius: 8px;
  background: var(--white);
  color: var(--text);
  font-size: 0.9rem;
  transition: var(--transition);
  min-width: 120px;
}

.status-select:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.1);
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
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
  transform: translateY(-2px);
}

.btn-update {
  background: linear-gradient(135deg, var(--success), #66bb6a);
  color: var(--white);
  box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.btn-update:hover {
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
}

.btn-delete {
  background: linear-gradient(135deg, var(--danger), #ef5350);
  color: var(--white);
  box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
}

.btn-delete:hover {
  box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
}

.action-form {
  margin: 0;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .page-header {
    padding: 20px;
  }

  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .header-title h1 {
    font-size: 2rem;
  }

  .stats-section {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
  }

  .actions-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .search-container {
    max-width: none;
  }

  .filter-buttons {
    justify-content: center;
  }

  .table-header {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }

  .data-table th,
  .data-table td {
    padding: 10px 8px;
    font-size: 0.85rem;
  }

  .status-form {
    flex-direction: column;
    gap: 8px;
  }

  .status-select {
    min-width: auto;
    width: 100%;
  }
}

@media (max-width: 480px) {
  body {
    padding: 10px;
  }

  .header-title h1 {
    font-size: 1.6rem;
  }

  .stats-section {
    grid-template-columns: 1fr;
  }

  .stat-card {
    padding: 20px;
  }

  .actions-bar {
    padding: 20px;
  }

  .table-header {
    padding: 15px 20px;
  }

  .data-table th,
  .data-table td {
    padding: 8px 6px;
    font-size: 0.8rem;
  }
}

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
  background: linear-gradient(135deg, var(--primary), #4db6ac);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--secondary), var(--primary));
}

/* ===== ANIMATIONS SUPPLÉMENTAIRES ===== */
.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

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

.stat-card {
  animation: fadeInUp 0.6s ease forwards;
  opacity: 0;
}
</style>
</head>
<body>

<div class="page-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Gestion des Commandes</h1>
        </div>
        <a href="../html/admin_dashboard.php" class="back-link">
            ← Retour au tableau de bord
        </a>
    </div>
</div>

<div class="container">
    <!-- Section Statistiques -->
    <div class="stats-section">
        <div class="stat-card">
            <span class="stat-icon">📊</span>
            <span class="stat-number"><?php echo number_format($stats['total_commandes']); ?></span>
            <span class="stat-label">Total Commandes</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon">💰</span>
            <span class="stat-number"><?php echo number_format($stats['total_revenus'], 2); ?></span>
            <span class="stat-label">Revenus Totaux</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon">⏳</span>
            <span class="stat-number"><?php echo $stats['en_attente']; ?></span>
            <span class="stat-label">En Attente</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon">✅</span>
            <span class="stat-number"><?php echo $stats['livre']; ?></span>
            <span class="stat-label">Livrées</span>
        </div>
    </div>

    <!-- Barre d'actions -->
    <div class="actions-bar">
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Rechercher une commande...">
            <span class="search-icon">🔍</span>
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-status="all">Tous</button>
            <button class="filter-btn" data-status="en_attente">En attente (<?php echo $stats['en_attente']; ?>)</button>
            <button class="filter-btn" data-status="confirmé">Confirmé (<?php echo $stats['confirme']; ?>)</button>
            <button class="filter-btn" data-status="livré">Livré (<?php echo $stats['livre']; ?>)</button>
            <button class="filter-btn" data-status="annulé">Annulé (<?php echo $stats['annule']; ?>)</button>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Tableau des commandes -->
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">
                📋 Liste des Commandes
            </div>
            <div class="table-count">
                <span id="visibleCount"><?php echo $result->num_rows; ?></span> commande(s)
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="data-table" id="commandesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset result pointer
                    $result->data_seek(0);
                    while ($commande = $result->fetch_assoc()): 
                    ?>
                    <tr data-status="<?php echo htmlspecialchars($commande['statut']); ?>">
                        <td><strong>#<?php echo htmlspecialchars($commande['id_commande']); ?></strong></td>
                        <td>
                            <span style="color: var(--primary); font-weight: 600;">
                                <?php echo htmlspecialchars($commande['username'] ?? 'Utilisateur supprimé'); ?>
                            </span>
                        </td>
                        <td>
                            <strong style="color: var(--success); font-size: 1.1rem;">
                                <?php echo number_format($commande['total'], 2); ?>DZ
                            </strong>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo htmlspecialchars($commande['statut']); ?>">
                                <?php echo ucfirst(htmlspecialchars($commande['statut'])); ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" class="status-form">
                                <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                                <select name="statut" class="status-select">
                                    <?php
                                    $statuts = ['en_attente', 'confirmé', 'livré', 'annulé'];
                                    foreach ($statuts as $statut) {
                                        $selected = ($commande['statut'] === $statut) ? 'selected' : '';
                                        echo "<option value=\"$statut\" $selected>" . ucfirst($statut) . "</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-update">
                                    Mettre à jour
                                </button>
                            </form>
                            <form method="post" class="action-form" onsubmit="return confirm('Voulez-vous vraiment supprimer la commande #<?php echo $commande['id_commande']; ?> ?');" style="margin-top: 8px;">
                                <input type="hidden" name="id_commande" value="<?php echo $commande['id_commande']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-delete">
                                    Supprimer
                                </button>
                            </form>
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
    const rows = document.querySelectorAll('#commandesTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('visibleCount').textContent = visibleCount;
});

// Filtrage par statut
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Retirer la classe active de tous les boutons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        // Ajouter la classe active au bouton cliqué
        this.classList.add('active');
        
        const status = this.dataset.status;
        const rows = document.querySelectorAll('#commandesTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        document.getElementById('visibleCount').textContent = visibleCount;
    });
});

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#commandesTable tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

// Auto-hide messages après 5 secondes
setTimeout(() => {
    const message = document.querySelector('.message');
    if (message) {
        message.style.transition = 'all 0.5s ease';
        message.style.opacity = '0';
        message.style.transform = 'translateY(-20px)';
        setTimeout(() => message.remove(), 500);
    }
}, 5000);
</script>

</body>
</html>
