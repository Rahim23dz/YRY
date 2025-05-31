<?php
session_start();
require_once('../php/db.php');

// Vérification de la session
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'vendeur' || empty($_SESSION['user_id'])) {
    die("⚠️ Erreur: Vous devez être connecté en tant que vendeur.");
}

$id_vendeur = intval($_SESSION['user_id']);

$message = "";

// Traitement du téléchargement de fichier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['recus']) && $_FILES['recus']['error'] === 0) {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES['recus']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../uploads/recus/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . "_" . basename($_FILES['recus']['name']);
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['recus']['tmp_name'], $targetFile)) {
                $relativePath = 'uploads/recus/' . $fileName;

                // Mise à jour du statut à 'en_attente' automatiquement
                $sql = "UPDATE vendeurs SET recus = ?, status = 'en_attente' WHERE id_vendeur = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $relativePath, $id_vendeur);

                if ($stmt->execute()) {
                    header("Location: ../html/login.php?upload=success");
                    exit;
                } else {
                    $message = "⚠️ Erreur lors de la sauvegarde en base de données.";
                }

                $stmt->close();
            } else {
                $message = "⚠️ Échec du téléchargement du fichier, veuillez réessayer.";
            }
        } else {
            $message = "⚠️ Type de fichier non supporté. Veuillez télécharger un PDF ou une image (JPG/PNG).";
        }
    } else {
        $message = "⚠️ Aucun fichier sélectionné.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Télécharger le reçu - YRY</title>
  <style>
    /* Variables harmonisées avec login.css */
    :root {
      --primary: #00796b;
      --secondary: #004d40;
      --bg: #fafafa;
      --text: #212121;
      --white: #fff;
    }

    /* Reset harmonisé */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header harmonisé avec login.css */
    header {
      background-color: #ffffff;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      gap: 15px;
    }

    header img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
    }

    header h1 {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text);
    }

    .back-btn {
      background-color: var(--primary);
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: var(--secondary);
    }

    /* Contenu principal */
    main {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    /* Container du formulaire harmonisé */
    .form-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 480px;
      width: 100%;
      padding: 40px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      text-align: center;
    }

    .form-container:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .form-container h2 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: var(--text);
      font-weight: 600;
    }

    /* Formulaire harmonisé */
    .form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      text-align: left;
    }

    .form label {
      font-weight: 600;
      color: var(--text);
      margin-bottom: 8px;
      display: block;
      text-align: left;
    }

    .form input[type="file"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: border-color 0.3s ease;
      outline: none;
    }

    .form input[type="file"]:focus {
      border-color: var(--primary);
      box-shadow: 0 0 8px rgba(0, 121, 107, 0.3);
    }

    /* Bouton harmonisé avec login.css */
    .form button[type="submit"] {
      background-color: var(--primary);
      color: white;
      padding: 14px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 10px;
      font-family: inherit;
    }

    .form button[type="submit"]:hover {
      background-color: var(--secondary);
    }

    /* Messages harmonisés */
    .message {
      font-size: 0.9rem;
      margin: 15px 0;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: 600;
    }

    .message.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .message:not(.success) {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    /* Footer harmonisé avec login.css */
    footer {
      background-color: var(--primary);
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: auto;
    }

    /* Responsive harmonisé avec login.css */
    @media (max-width: 768px) {
      header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 10px;
      }

      header h1 {
        text-align: center;
      }

      .back-btn {
        align-self: flex-start;
      }

      .form-container {
        padding: 25px;
        margin: 20px;
      }

      main {
        padding: 20px 10px;
      }
    }

    @media (max-width: 480px) {
      .form-container {
        padding: 20px;
      }

      header {
        padding: 10px 15px;
      }

      .form input[type="file"] {
        padding: 10px 12px;
      }

      .form button[type="submit"] {
        padding: 12px 16px;
      }
    }
  </style>
</head>
<body>
  <header>
    <img src="../img/logo.jpg" alt="Logo de YRY" />
    <h1>YRY</h1>
    <a href="../html/login.php" class="back-btn">← Retour</a>
  </header>

  <main>
    <div class="form-container">
      <h2>Veuillez renvoyer le reçu</h2>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="form">
        <label for="recus">Choisir le fichier reçu (PDF ou image) :</label>
        <input type="file" name="recus" id="recus" accept=".pdf,image/jpeg,image/png" required />
        <button type="submit">Envoyer le reçu</button>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 YRY. Tous droits réservés.</p>
  </footer>
</body>
</html>