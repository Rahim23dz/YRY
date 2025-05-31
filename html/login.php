<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../php/db.php'; // الاتصال بقاعدة البيانات

// عرض رسالة الخطأ إذا كانت موجودة
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger' style='margin: 15px; padding: 10px; border: 1px solidrgb(220, 53, 53); background-color: #f8d7da; color: #721c24; border-radius: 5px;'>"
        . $_SESSION['error_message'] .
        "</div>";
    unset($_SESSION['error_message']);
}

// جلب قائمة الولايات
$wilayas = [];
$result = $conn->query("SELECT id_wilaya AS id, nom_wilaya AS nom FROM wilaya ORDER BY nom_wilaya ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wilayas[] = $row;
    }
}
?>



<!DOCTYPE html>

<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion - YRY</title>
  <link rel="stylesheet" href="../css/login.css" />
</head>
<body>
  <header>
    <img src="../img/logo.jpg" alt="Logo de YRY" />
    <h1>YRY</h1>
    <a href="home.php" class="back-btn">← Accueil</a>
  </header>

  <main>
    <div class="form-container">
      <div class="tabs">
        <button id="loginTab" class="active">Se connecter</button>
        <button id="signupTab">S'inscrire</button>
      </div>

  <!-- Formulaire de connexion -->
  <form id="loginForm" class="form" method="POST" action="../php/login.php">
    <label for="loginUser">Nom d'utilisateur</label>
    <input type="text" id="loginUser" name="loginUser" required />

    <label for="loginPass">Mot de passe</label>
    <input type="password" id="loginPass" name="loginPass" required />

    <button type="submit">Connexion</button>
    <div id="loginMessage" class="message"></div>
  </form>
<div id="message" style="margin-top: 10px; color: red;"></div>
  <!-- Formulaire d'inscription -->
  <form id="signupForm" class="form" method="POST" action="../php/signup.php" enctype="multipart/form-data" style="display:none;">
    <label for="signupRole">Rôle</label>
    <select id="signupRole" name="signupRole" required>
      <option value="">-- Sélectionner --</option>
      <option value="client">Client</option>
      <option value="vendeur">Vendeur</option>
    </select>

    <label for="signupUser">Nom d'utilisateur</label>
    <input type="text" id="signupUser" name="signupUser" required />

    <label for="signupEmail">Email</label>
    <input type="email" id="signupEmail" name="signupEmail" required />

    <label for="signupPhone">Numéro de téléphone</label>
    <input type="tel" id="signupPhone" name="signupPhone" required />

    <label for="signupPass">Mot de passe</label>
    <input type="password" id="signupPass" name="signupPass" required />

    <label for="signupImage">Photo de profil</label>
    <input type="file" id="signupImage" name="signupImage" accept="image/*" />

    <label for="signupAdresse">Adresse exacte</label>
    <input type="text" id="signupAdresse" name="signupAdresse" required />

    <label for="signupWilaya">Wilaya</label>
    <select id="signupWilaya" name="signupWilaya" required>
      <?php foreach ($wilayas as $w): ?>
        <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['nom']) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Champs spécifiques au vendeur -->
    <div id="vendeurFields" style="display: none;">
      <label for="storeName">Nom du magasin</label>
      <input type="text" id="storeName" name="storeName" />

      <label for="storeAddress">Adresse du magasin</label>
      <input type="text" id="storeAddress" name="storeAddress" />
    </div>

    <button type="submit">Créer un compte</button>
    <div id="signupMessage" class="message"></div>
  </form>
</div>


  </main>

  <footer>
    <p>&copy; 2025 YRY. Tous droits réservés.</p>
  </footer>
</body>
</html>
  <script src="../js/login.js"></script>


