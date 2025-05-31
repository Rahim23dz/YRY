<?php
session_start();
include("../php/db.php");

$message = "";

// التأكد من أن المستخدم مسجل دخول كـ بائع


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendeur') {
    $_SESSION['error_message'] = "يجب أن تكون بائعاً لإضافة منتج.";
    header('Location: login.php');
    exit();
}
 else {
    $id_vendeur = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = mysqli_real_escape_string($conn, $_POST['nom']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $categorie_nom = $_POST['categorie'];
        $sous_type = mysqli_real_escape_string($conn, $_POST['sous_type']);
        $prix = $_POST['prix'];
        $id_vehicule = $_POST['id_vehicule'];
        $etat = (int) $_POST['etat'];

        if (
            empty($nom) || empty($description) || empty($categorie_nom) ||
            empty($sous_type) || empty($prix) || empty($id_vehicule) || empty($etat)
        ) {
            $message = "يرجى ملء جميع الحقول.";
        } elseif ($etat < 1 || $etat > 10) {
            $message = "قيمة الحالة يجب أن تكون بين 1 و10.";
        } else {
            // خريطة ربط الفئات مع ID
            $map = [
                'suspension' => 1,
                'freinage' => 2,
                'moteur' => 3,
                'divers' => 4,
                'embrayage_boite' => 5,
                'optiques' => 6,
                'essuie_glaces' => 7,
                'pneus' => 9,
                'chauffage_climatisation' => 10,
                'Démarrage électrique' => 11
            ];

            if (!isset($map[$categorie_nom])) {
                $message = "الفئة غير صالحة.";
            } else {
                $categorie_id = $map[$categorie_nom];

                // التأكد من وجود الفئة في قاعدة البيانات
                $verif = mysqli_query($conn, "SELECT id_categorie FROM categories WHERE id_categorie = '$categorie_id'");
                if (mysqli_num_rows($verif) == 0) {
                    $message = "الفئة غير موجودة في قاعدة البيانات.";
                } else {
                    // التحقق من وجود السيارة
                    $verif_vehicule = mysqli_query($conn, "SELECT id_vehicule FROM vehicules WHERE id_vehicule = '$id_vehicule'");
                    if (mysqli_num_rows($verif_vehicule) == 0) {
                        $message = "نوع السيارة غير موجود في قاعدة البيانات.";
                    } else {
                        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                            $upload_dir = "../uploads/products/";
                            if (!is_dir($upload_dir)) {
                                mkdir($upload_dir, 0777, true);
                            }

                            $image_name = basename($_FILES['image']['name']);
                            $image_name = mysqli_real_escape_string($conn, $image_name);
                            $image_path = $upload_dir . $image_name;

                            $image_type = $_FILES['image']['type'];
                            if (!in_array($image_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                                $message = "الرجاء رفع صورة بصيغة صحيحة (JPG, PNG, GIF).";
                            } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                                $result = mysqli_query($conn, "SELECT id FROM sous_types WHERE name = '$sous_type'");
                                if (mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $id_sous_type = $row['id'];

                                    $sql = "INSERT INTO products (nom, description, categorie, id_sous_type, prix, image, id_vendeur, id_vehicule, etat)
                                            VALUES ('$nom', '$description', '$categorie_id', '$id_sous_type', '$prix', '$image_name', '$id_vendeur','$id_vehicule', '$etat')";

                                    if (mysqli_query($conn, $sql)) {
                                        $pages = [
                                            'Amortisseurs' => 'amortisseur.php',
                                            'Suspension d’essieux' => 'suspension_d_essieux.php',
                                            'Transmission' => 'transmission.php',
                                            'Rotules / Direction' => 'rotules_direction.php',
                                            'Moyeux et roulements' => 'moyeux_roulements.php',
                                            'Direction' => 'direction.php',
                                            'Butées' => 'butees.php',
                                            'Ressorts et Soufflets' => 'ressorts_soufflets.php',

                                            'Plaquettes de frein' => 'plaquettes_de_frein.php',
                                            'Disques de frein' => 'disques_de_frein.php',
                                            'Hydraulique' => 'hydraulique.php',
                                            'Freins à tambours' => 'freins_a_tambours.php',
                                            'Capteurs et câbles de freinage' => 'capteurs_cables_freinage.php',
                                            'Assistance au freinage' => 'assistance_au_freinage.php',
                                            'Outils freinage' => 'outils_freinage.php',

                                            'Filtre à huile' => 'filtre_a_huile.php',
                                            'Batterie 12V' => 'batterie_12v.php',
                                            'Huile moteur 5W-30' => 'huile_moteur_5w_30.php',
                                            'Filtre à air' => 'filtre_a_air.php',
                                            'Kit de courroie de distribution' => 'kit_courroie_distribution.php',
                                            'Pompe à carburant' => 'pompe_carburant.php',
                                            'Soupapes et poussoirs' => 'soupapes_poussoirs.php',
                                            'Courroie d’accessoires' => 'courroie_accessoires.php',
                                            'Pompe à huile' => 'pompe_huile.php',
                                            'Culasse' => 'culasse.php',
                                            'Carter d’huile' => 'carter_huile.php',
                                            'Système de refroidissement' => 'systeme_refroidissement.php',

                                            'Kit_de_bougies' => 'kit_de_bougies.php',
                                            'Pompe à eau' => 'pompe_a_eau.php',
                                            'Boîtier de filtre à carburant' => 'boitier_filtre_carburant.php',
                                            'Rétroviseur extérieur' => 'retroviseur_exterieur.php',
                                            'Silencieux' => 'silencieux.php',

                                            'Embrayage et Volant-moteur' => 'embrayage_et_volant_moteur.php',
                                            'Accessoires de boîte de vitesse' => 'accessoires_boite_vitesse.php',
                                            'Autres pièces d\'Embrayage' => 'autres_pieces_embrayage.php',

                                            'Optiques et Phares' => 'optiques_et_phares.php',
                                            'Ampoules, Éclairage avant' => 'ampoules_eclairage_avant.php',
                                            'Ampoules, Éclairage arrière' => 'ampoules_eclairage_arriere.php',
                                            'Ampoules, Éclairage intérieur et signalisation' => 'ampoules_eclairage_interieur_signalisation.php',

                                            'Moteur d\'essuie-glace' => 'moteur_essuie_glace.php',
                                            'Pompe de lave-glace' => 'pompe_lave_glace.php',
                                            'Lave-glace' => 'lave_glace.php',
                                            'Balai d\'essuie-glace' => 'balai_essuie_glace.php',

                                            'Pneumatiques' => 'pneumatiques.php',
                                            'Chaînes-neiges et Equipements Roue' => 'chaines_neiges_equipements_roue.php',
                                            'Outils pneu' => 'outils_pneu.php',

                                            'Climatisation' => 'climatisation.php',
                                            'Chauffage et Ventilation' => 'chauffage_et_ventilation.php',

                                            'Alternateurs' => 'alternateurs.php',
                                            'Démarreurs' => 'demarreurs.php',
                                            'Outils Batteries' => 'outils_batteries.php',
                                        ];

                                        $page = isset($pages[$sous_type]) ? $pages[$sous_type] : 'vendeur.php';
                                        echo "<script>alert('تمت إضافة المنتج بنجاح.'); window.location.href='$page';</script>";
                                        exit;
                                    } else {
                                        $message = "خطأ في إدخال البيانات: " . mysqli_error($conn);
                                    }
                                } else {
                                    $message = "النوع الفرعي غير موجود في قاعدة البيانات.";
                                }
                            } else {
                                $message = "فشل في رفع الصورة.";
                            }
                        } else {
                            $message = "الرجاء رفع صورة للمنتج.";
                        }
                    }
                }
            }
        }
    }
}

// عرض الرسائل إن وجدت
if (!empty($message)) {
    echo "<script>alert('$message');</script>";
}
?>


<!-- HTML Form remains the same -->


<!DOCTYPE html>
<html lang="ar">
<head>
  <link rel="stylesheet" href="../css/vondeur.css">
    <meta charset="UTF-8">
    <title>إضافة منتج</title>
    

</head>
<body>
  <header>
        <div class="header-top">
            <div class="header-left">
                <img src="../img/logo.jpg" alt="Logo YRY">
                <h1>YRY</h1>
            </div>
            
           
            
           
        </div>
        
         <nav class="header-nav">
                <a href="home.php" class="nav-link">
                    <span class="nav-icon">🏠</span>
                    Accueil
                </a>
                
                <a href="amortisseur.php" class="nav-link">
                    <span class="nav-icon">🔧</span>
                    Amortisseurs
                </a>
                
                <a href="disques_de_frein.php" class="nav-link">
                    <span class="nav-icon">🛞</span>
                    Freins
                </a>
                
                <a href="batterie.php" class="nav-link">
                    <span class="nav-icon">🔋</span>
                    Batterie
                </a>
                
                <a href="pneu.php" class="nav-link">
                    <span class="nav-icon">⚫</span>
                    Pneus
                </a>
                
                <a href="bougie.php" class="nav-link">
                    <span class="nav-icon">⚡</span>
                    Bougies
                </a>
                
                <a href="huile.php" class="nav-link">
                    <span class="nav-icon">🛢️</span>
                    Huile
                </a>
                
               
              
            </nav>
    </header>


<h2 style="text-align:center;">إضافة منتج جديد</h2>

<form id="formAjoutProduit" action="vondeur.php" method="POST" enctype="multipart/form-data">

    <label>اسم المنتج:</label>
    <input type="text" name="nom" required>

    <label>الوصف:</label>
    <textarea name="description" required></textarea>

    <label>الفئة:</label>
    <select id="categorieProduit" name="categorie" required>
    <option value="suspension">Direction / Suspension / Train</option>
    <option value="freinage">Freinage</option>
    <option value="moteur">Entretien Moteur</option>
    <option value="divers">Divers</option>
    <option value="embrayage_boite">Embrayage et Boîte de vitesse</option>
    <option value="optiques">Optiques / Phares / Ampoules</option>
    <option value="essuie_glaces">Essuie-glaces et pièces</option>
    <option value="Pneus et Equipements Roue">Pneus et Equipements Roue</option>
    <option value="chauffage_climatisation">Chauffage et Climatisation</option>
    <option value="Démarrage électrique">Démarrage électrique</option>
</select>


    <label>النوع الفرعي:</label>
    <select id="sousTypeProduit" name="sous_type" required>
        <!-- الخيارات ستتغير بناءً على الفئة -->
    </select>
    <label>نوع التاكسي:</label>
    <select name="id_vehicule" required>
    <option value="">-- اختر نوع السيارة --</option>
    <?php
    // استعلام لجلب جميع المركبات من جدول vehicules
    $query = "SELECT id_vehicule, marque, modele FROM vehicules";
    $result = mysqli_query($conn, $query);

    // التكرار على المركبات وعرضها في القائمة
    while ($veh = mysqli_fetch_assoc($result)) {
        echo "<option value='{$veh['id_vehicule']}'>
                {$veh['marque']} - {$veh['modele']}
              </option>";
    }
    ?>
</select>







    <label>السعر (DA):</label>
    <input type="number" name="prix" step="0.01" required>

    <label>صورة المنتج:</label>
    <input type="file" name="image" accept="image/*" required>
    <label for="etat">الحالة (1 = قديم / 10 = جديد):</label>
<select name="etat" id="etat" required>
  <?php
  for ($i = 1; $i <= 10; $i++) {
      echo "<option value=\"$i\">$i / 10</option>";
  }
  ?>
</select>


    <button type="submit">إضافة المنتج</button>

    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
</form>

</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formAjoutProduit');
    const categorieProduit = document.getElementById('categorieProduit');
    const sousTypeProduit = document.getElementById('sousTypeProduit');

    function updateSousTypes() {
        const selectedCategory = categorieProduit.value;
        let sousTypes = [];

if (selectedCategory === 'suspension') {
    sousTypes = [
        'Amortisseurs', 'Suspension d’essieux', 'Transmission',
        'Rotules / Direction', 'Moyeux et roulements', 'Direction',
        'Butées', 'Ressorts et Soufflets'
    ];
} else if (selectedCategory === 'freinage') {
    sousTypes = [
        'Plaquettes de frein', 'Disques de frein', 'Hydraulique',
        'Freins à tambours', 'Capteurs et câbles de freinage',
        'Assistance au freinage', 'Outils freinage'
    ];
} else if (selectedCategory === 'moteur') {
    sousTypes = [
        'Filtre à huile', 'Batterie 12V', 'Huile moteur 5W-30',
        'Filtre à air', 'Kit de courroie de distribution',
        'Pompe à carburant',
        'Soupapes et poussoirs', 'Courroie d’accessoires',
        'Pompe à huile', 'Culasse', 'Carter d’huile',
        'Système de refroidissement'
    ];
} else if (selectedCategory === 'divers') {
    sousTypes = [
        'Kit_de_bougies', 'Pompe à eau', 'Boîtier de filtre à carburant',
        'Rétroviseur extérieur', 'Silencieux'
    ];
} else if (selectedCategory === 'embrayage_boite') {
    sousTypes = [
        'Embrayage et Volant-moteur', 'Accessoires de boîte de vitesse',
        'Autres pièces d\'Embrayage'
    ];
} else if (selectedCategory === 'optiques') {
    sousTypes = [
        'Optiques et Phares', 'Ampoules, Éclairage avant',
        'Ampoules, Éclairage arrière', 'Ampoules, Éclairage intérieur et signalisation'
    ];
} else if (selectedCategory === 'essuie_glaces') {
    sousTypes = [
        'Moteur d\'essuie-glace', 'Pompe de lave-glace',
        'Lave-glace', 'Balai d\'essuie-glace'
    ];
} else if (selectedCategory === 'Pneus et Equipements Roue') {
    sousTypes = [
        'Pneumatiques', 'Chaînes-neiges et Equipements Roue',
        'Outils pneu'
    ];
} else if (selectedCategory === 'chauffage_climatisation') {
    sousTypes = [
        'Climatisation', 'Chauffage et Ventilation'
    ];
} else if (selectedCategory === 'Démarrage électrique') {
    sousTypes = [
        'Alternateurs', 'Démarreurs', 'Outils Batteries'
    ];
}




        sousTypeProduit.innerHTML = '<option value="">-- Choisissez un sous-type --</option>';
        sousTypes.forEach(type => {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            sousTypeProduit.appendChild(option);
        });
    }

    categorieProduit.addEventListener('change', updateSousTypes);

    // تحديث أولي عند تحميل الصفحة
    updateSousTypes();
});
</script>