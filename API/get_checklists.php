<?php
//require_once 'controlelogin.php'; // login controle
require_once '../dbconnect.php'; // veilige database connectie

header('Content-Type: application/json');

// hardcoded querie SELECT - geen gebruikersinput
$sql = "SELECT id, titel, land, jaar 
        FROM tbl_checklist 
        ORDER BY id DESC";

$result = $conn->query($sql);

if (!$result) { // checken of query gelukt is voor het resultaat gebruikt word
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$checklists = [];

while ($row = $result->fetch_assoc()) {
    $checklists[] = $row;
}

echo json_encode($checklists); // Veilige JSON output

$conn->close();
?>