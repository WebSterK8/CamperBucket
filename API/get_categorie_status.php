<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');


// hardcoded querie SELECT - geen gebruikersinput
$sql = "SELECT categorie, persoon, checked
        FROM tbl_categorie_status";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$statussen = [];

while ($row = $result->fetch_assoc()) {
    $statussen[] = $row;
}

echo json_encode($statussen); // Veilige JSON output

$conn->close();
?>
