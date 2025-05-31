<?php
session_start();
include '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // تحويل لصفحة تسجيل الدخول
    exit;
}
$produits = [];
$totalGeneral = 0;

if (isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];

    $sql = "SELECT p.id_product, p.nom, p.prix, pa.quantite, (p.prix * pa.quantite) AS total
            FROM panier pa
            JOIN products p ON pa.id_product = p.id_product
            WHERE pa.id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $produits[] = $row;
        $totalGeneral += $row['total'];
    }

    $stmt->close();
} else {
    echo "<p style='color:red;'>Veuillez vous connecter pour voir votre panier.</p>";
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Panier</title>
    <link rel="stylesheet" href="../css/panier.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <header>
  <!-- القسم العلوي -->
  <div class="header-top">
  <div class="header-left">
    <img src="../img/logo.jpg" alt="Logo YRY">
    <h1>YRY</h1>
  </div>

  
<div class="header-actions">
  <?php
    if (isset($_SESSION['username'])) {
        echo '<a href="../php/profil.php" class="btn-header icon-btn">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M5.5 21a7.5 7.5 0 0 1 13 0"></path>
          </svg>
        </span>
        <span class="label">Profil</span>
      </a>';
    } else {
        echo '<a href="../php/login.html" class="btn-header icon-btn">
          <span class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
              <path d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"></path>
              <path d="M6 21v-2c0-2.21 3.58-4 6-4s6 1.79 6 4v2"></path>
            </svg>
          </span>
          <span class="label">Se connecter</span>
        </a>';
    }



// زر Besoin d'aide
echo '<a href="../html/aide.html" class="btn-header icon-btn besoin-aide-btn">
        <span class="icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" viewBox="0 0 24 24">
            <path d="M12 18h.01"></path>
            <path d="M12 14a4 4 0 1 0-4-4"></path>
            <path d="M12 2a10 10 0 1 1-10 10"></path>
          </svg>
        </span>
        <span class="label">Besoin d\'aide</span>
      </a>';
  ?>
</div>




  <!-- القسم السفلي -->
   <!-- زر أزرق كـ button -->
<button class="btn-blue" onclick="location.href='../html/contact_support.html'">Support</button>


</header>
<div class="progress-container">
  <div class="progress-step active">
    <div class="step-number">1</div>
    <div class="step-title">اشري</div>
  </div>
  <div class="progress-line"></div>
  <div class="progress-step">
    <div class="step-number">2</div>
    <div class="step-title">يبعتلك البايع</div>
  </div>
  <div class="progress-line"></div>
  <div class="progress-step">
    <div class="step-number">3</div>
    <div class="step-title">كي تعجبك خلص</div>
  </div>
</div>

<div class="h1">
  <h1>🛒 Votre Panier</h1></div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
      <script>
          Swal.fire("Supprimé!", "Produit supprimé du panier.", "success");
      </script>
  <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'cleared'): ?>
      <script>
          Swal.fire("Vider le panier", "Tous les produits ont été supprimés.", "success");
      </script>
  <?php endif; ?>

  <?php if (empty($produits)): ?>
      <div class="empty-cart-message" style="text-align: center; padding: 40px;">
    <h2 style="color: #555;">Votre panier est vide...</h2>
    <p style="font-size: 18px; color: #777;">Sur Mister-Auto, trouvez les pièces adaptées à votre véhicule et commandez en quelques clics !</p>
    <a href="../html/home.php" class="btn-retour" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: orange; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold;">
        ⬅ Continuer mes achats 
    </a>
</div>

  <?php else: ?>

  <div class="container">
      <table>
          <thead>
          <tr>
              <th>Produit</th>
              <th>Prix</th>
              <th>Quantité</th>
              <th>Total</th>
              <th>Action</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($produits as $row): ?>
              <tr>
                  <td><?= htmlspecialchars($row['nom']); ?></td>
                  <td><?= number_format($row['prix'], 2); ?> DZD</td>
                  <td>
                      <input type="number" value="<?= $row['quantite'] ?>" min="1"
                             data-id="<?= $row['id_product'] ?>" class="quantite-input">
                  </td>
                  <td><?= number_format($row['total'], 2); ?> DZD</td>
                  <td>
                      <a href="../php/supprimer_panier.php?id_product=<?= $row['id_product']; ?>" class="btn-supprimer">🗑 Supprimer</a>
                  </td>
              </tr>
          <?php endforeach; ?>

          <tr>
              <td colspan="3"><strong>Total Général</strong></td>
              <td><strong><?= number_format($totalGeneral, 2); ?> DZD</strong></td>
              <td>
                  <a href="../php/vider_panier.php" class="btn-vider">🧹 Vider le Panier</a>
                  <!-- زر Confirmer جديد -->
                  <button class="btn-confirmer" id="btnConfirmer">Confirmer la commande</button>
              </td>
          </tr>
          </tbody>
      </table>
  </div>

  <script>
      document.querySelectorAll('.quantite-input').forEach(input => {
          input.addEventListener('change', function () {
              const idProduct = this.dataset.id;
              const quantite = this.value;

              fetch('../php/modifier_quantite.php', {
                  method: 'POST',
                  headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                  body: `id_product=${idProduct}&quantite=${quantite}`
              }).then(response => location.reload());
          });
      });

      // SweetAlert Confirmation
      document.getElementById('btnConfirmer').addEventListener('click', function() {
          Swal.fire({
              title: 'Êtes-vous sûr ?',
              text: "Une fois la commande confirmée, vous ne pourrez plus annuler.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Confirmer',
              cancelButtonText: 'Annuler',
              reverseButtons: true
          }).then((result) => {
              if (result.isConfirmed) {
                  // توجيه إلى صفحة الدفع أو تأكيد الطلب
                  window.location.href = "../php/confirmer_commande.php";
              }
          });
      });
  </script>

  <?php endif; ?>

</body>
</html>
