<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// REIZENOVERZICHT OPHALEN voor bucketlist-pagina en timeline

// hardcoded query SELECT - geen gebruikersinput
$sql = "SELECT id, intro, land, beschrijving, foto, foto_alt,
               start_jaar, start_maand, start_dag,
               eind_jaar, eind_maand, eind_dag
        FROM tbl_reizen
        ORDER BY start_jaar ASC, start_maand ASC, start_dag ASC, id ASC";

$result = $conn->query($sql);

if (!$result) { // checken of query gelukt is voor het resultaat gebruikt wordt
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$reizen = [];

while ($row = $result->fetch_assoc()) {
    $reizen[] = $row;
}

echo json_encode($reizen); // Veilige JSON output

$conn->close();
?>
