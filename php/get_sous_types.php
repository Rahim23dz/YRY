<?php
include("db.php");
header('Content-Type: application/json');

if (isset($_GET['categorie_id'])) {
    $cat_id = intval($_GET['categorie_id']);
    $result = mysqli_query($conn, "SELECT id, name FROM sous_types WHERE categorie_id = '$cat_id'");

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode([]);
}
