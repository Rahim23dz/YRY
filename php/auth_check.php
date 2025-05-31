<?php
// session_start();

// if (
//     !isset($_SESSION['id_vendeur']) || 
//     !isset($_SESSION['role']) || 
//     $_SESSION['role'] !== 'vendeur'
// ) {
//     header("Location: ../html/login.php");
//     exit;
// }

// include 'db.php';

// $id_vendeur = $_SESSION['id_vendeur'];

// $sql = "SELECT status FROM vendeurs WHERE id_vendeur = ?";
// $stmt = $conn->prepare($sql);

// if (!$stmt) {
//     session_unset();
//     session_destroy();
//     header("Location: ../html/login.php");
//     exit;
// }

// $stmt->bind_param("i", $id_vendeur);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($row = $result->fetch_assoc()) {
//     $status = strtolower(trim($row['status']));

//     if ($status === 'refuse') {
//         session_unset();
//         session_destroy();
//         header("Location: ../php/logout.php");
//         exit;
//     }
//     else if ($status !== 'accepte') {
//         session_unset();
//         session_destroy();
//         header("Location: ../php/logout.php");
//         exit;
//     }
// } else {
//     session_unset();
//     session_destroy();
//     header("Location: ../php/logout.php");
//     exit;
// }

// $stmt->close();
// $conn->close();
?>
