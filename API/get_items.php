<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');


// hardcoded querie SELECT - geen gebruikersinput - alleen basisitems (checklist_id IS NULL)
$sql = "SELECT id, naam, categorie, standaard
        FROM tbl_items
        WHERE checklist_id IS NULL
        ORDER BY categorie, naam";

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

 