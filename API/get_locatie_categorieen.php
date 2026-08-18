<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// hardcoded querie SELECT - geen gebruikersinput
$sql = "SELECT slug, naam, positie
        FROM tbl_locatie_categorie
        ORDER BY positie, naam";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$categorieen = [];

while ($row = $result->fetch_assoc()) {
    $categorieen[] = $row;
}

echo json_encode($categorieen); // Veilige JSON output

$conn->close();
?>
