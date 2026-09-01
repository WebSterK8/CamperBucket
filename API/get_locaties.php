<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// Input validatie: verplicht + numeriek
if (empty($_GET['reis_id']) || !is_numeric($_GET['reis_id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Ongeldige reis_id.']); // Veilige JSON output
    exit;
}

// Input opschonen met (int) - altijd een getal
$reisId = (int) $_GET['reis_id'];

$sql = "SELECT id, reis_id, naam, beschrijving, categorie, lat, lon, link
        FROM tbl_locaties
        WHERE reis_id = ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
$stmt->bind_param("i", $reisId);
$stmt->execute();
$result = $stmt->get_result();

$locaties = [];

while ($row = $result->fetch_assoc()) {
    $row['lat'] = (float) $row['lat'];
    $row['lon'] = (float) $row['lon'];
    $locaties[] = $row;
}

echo json_encode($locaties); // Veilige JSON output

$stmt->close();
$conn->close();
?>
