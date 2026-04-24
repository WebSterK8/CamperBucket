<?php
require_once 'controlelogin.php';
require_once '../dbconnect.php';

header('Content-Type: application/json');

//SELECT * FROM tbl_items WHERE categorie = 'food';
//SELECT * FROM tbl_items WHERE categorie = 'stuff';

$sql = "SELECT id, naam, categorie, standaard 
        FROM tbl_items 
        ORDER BY categorie, naam";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']);
    exit;
}

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);

$conn->close();
?>

 