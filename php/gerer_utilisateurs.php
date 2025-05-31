<?php
session_start();
include 'db.php';

// --- إحصائيات ---
$totalClients = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$totalVendeurs = $conn->query("SELECT COUNT(*) FROM vendeurs")->fetch_row()[0];
$totalUtilisateurs = $totalClients + $totalVendeurs;
$nouveauxClients = $conn->query("SELECT COUNT(*) FROM users WHERE date_inscription >= CURDATE() - INTERVAL 7 DAY")->fetch_row()[0];
$nouveauxVendeurs = $conn->query("SELECT COUNT(*) FROM vendeurs WHERE date_inscription >= CURDATE() - INTERVAL 7 DAY")->fetch_row()[0];

$filtre_role = $_GET['filtre_role'] ?? '';
$recherche = $_GET['recherche'] ?? '';

// --- استعلام موحد ---
$query = "
    SELECT id_user AS id, username, email, profile_image, role, phone, NULL AS store_name, NULL AS store_address, date_inscription, 'client' AS user_type 
    FROM users
    UNION ALL
    SELECT id_vendeur AS id, username, email, profile_image, 'vendeur' AS role, phone, store_name, store_address, date_inscription, 'vendeur' AS user_type 
    FROM vendeurs
";

$result = $conn->query($query);
$utilisateurs = [];

while ($row = $result->fetch_assoc()) {
    if ($filtre_role && $row['user_type'] != $filtre_role) continue;
    if ($recherche && stripos($row['username'], $recherche) === false && stripos($row['email'], $recherche) === false) continue;
    $utilisateurs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gérer les Utilisateurs</title>
    <link rel="stylesheet" href="../css/gerer_utilisateurs.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f7f9fc;
            color: #333;
        }
        h1 {
            margin-bottom: 25px;
        }
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .card {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 1;
            text-align: center;
        }
        .card h3 {
            margin-bottom: 10px;
            font-weight: 600;
            color: #555;
        }
        .card p {
            font-size: 1.8rem;
            font-weight: bold;
            color: #222;
        }
        .filters form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filters input[type="text"] {
            padding: 8px 12px;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .filters select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .filters button {
            padding: 8px 18px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .filters button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        thead th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        tbody tr {
            border-bottom: 1px solid #ddd;
        }
        tbody tr:hover {
            background-color: #f1faff;
        }
        tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            color: #444;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }
        .initial {
            width: 40px;
            height: 40px;
            background-color: #007bff;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            user-select: none;
        }
        .role-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            display: inline-block;
            text-transform: capitalize;
        }
        .role-badge.client {
            background-color: #28a745;
        }
        .role-badge.vendeur {
            background-color: #ffc107;
            color: #222;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #a71d2a;
        }
        td:nth-child(6), td:nth-child(7) {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <script>
       function confirmDelete(id, type) {
    if (confirm(`Voulez-vous vraiment supprimer l'utilisateur ${id} de type ${type} ?`)) {
        // إنشاء نموذج مؤقت للإرسال
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../php/supprimer_utilisateur.php'; // عدل حسب مسار سكريبت الحذف

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id_user';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}

    </script>
</head>
<body>

    <h1>Gestion des Utilisateurs</h1>
     <a href="../html/admin_dashboard.php" class="back-link">
            ← Retour à la liste
        </a>

    <div class="stats">
        <div class="card">
            <h3>Clients</h3>
            <p><?= $totalClients ?></p>
        </div>
        <div class="card">
            <h3>Vendeurs</h3>
            <p><?= $totalVendeurs ?></p>
        </div>
        <div class="card">
            <h3>Total</h3>
            <p><?= $totalUtilisateurs ?></p>
        </div>
        <div class="card">
            <h3>Nouveaux (7j)</h3>
            <p><?= $nouveauxClients + $nouveauxVendeurs ?></p>
        </div>
    </div>

    <div class="filters">
        <form method="GET">
            <input type="text" name="recherche" placeholder="Rechercher..." value="<?= htmlspecialchars($recherche) ?>">
            <select name="filtre_role">
                <option value="">Tous</option>
                <option value="client" <?= $filtre_role == 'client' ? 'selected' : '' ?>>Clients</option>
                <option value="vendeur" <?= $filtre_role == 'vendeur' ? 'selected' : '' ?>>Vendeurs</option>
            </select>
            <button type="submit">Filtrer</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Magasin</th>
                <th>Adresse</th>
                <th>Date Inscription</th>
                <th>Action</th>
            </tr>
        </thead>
       <tbody>
<?php if (count($utilisateurs) > 0): ?>
    <?php foreach ($utilisateurs as $user): ?>
        <tr>
            <td>
                <?php 
                $defaultImage = '../uploads/profil/default.jpg';
                if (!empty($user['profile_image']) && file_exists('../uploads/profil/' . $user['profile_image'])): ?>
                    <img src="../uploads/profil/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="avatar" />
                <?php else: ?>
                    <img src="<?= $defaultImage ?>" alt="Default Profile Image" class="avatar" />
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
            <td>
                <span class="role-badge <?= $user['user_type'] ?>"><?= ucfirst($user['user_type']) ?></span>
            </td>
            <td><?= htmlspecialchars($user['store_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($user['store_address'] ?? '-') ?></td>
            <td><?= htmlspecialchars($user['date_inscription']) ?></td>
            <td>
                <button class="btn-danger" onclick="confirmDelete(<?= $user['id'] ?>, '<?= $user['user_type'] ?>')" title="Supprimer">🗑️</button>
                <a href="../php/modifier_utilisateur.php?id=<?= $user['id'] ?>&type=<?= $user['user_type'] ?>" 
                   class="btn-modifier" 
                   style="margin-left:8px; padding:7px 12px; background-color:#007bff; color:#fff; border:none; border-radius:5px; text-decoration:none; font-size:1rem;"
                   title="Modifier">✏️</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="9" style="text-align:center;">Aucun utilisateur trouvé.</td></tr>
<?php endif; ?>
</tbody>

    </table>
</body>
</html>
