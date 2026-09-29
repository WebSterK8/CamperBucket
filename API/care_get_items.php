<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');


// hardcoded querie SELECT - geen gebruikersinput
$sql = "SELECT id, naam, notitie, categorie, positie, checked, checked_kaatje, checked_ben, toegewezen
        FROM tbl_care_items
        ORDER BY categorie, positie, id";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items); // Veilige JSON output

$conn->close();
?>
