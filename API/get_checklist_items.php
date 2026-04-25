<?php
require_once 'controlelogin.php'; // login controle
require_once '../dbconnect.php'; // veilige database connectie

header('Content-Type: application/json');

if (!isset($_GET['checklist_id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'checklist_id ontbreekt']); // Veilige JSON output
    exit;
}

// Input opschonen met (int) - altijd een getal
$checklistId = (int) $_GET['checklist_id'];

$sql = "
    SELECT 
        ci.checklist_id,
        ci.item_id,
        ci.checked,
        i.naam,
        i.categorie
    FROM tbl_checklist_items ci
    INNER JOIN tbl_items i ON ci.item_id = i.id
    WHERE ci.checklist_id = ?
    ORDER BY i.categorie, i.naam
";

// Prepared Statements
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['message' => 'Prepare failed', 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("i", $checklistId);
$stmt->execute();

$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items); // Veilige JSON output

$stmt->close();
$conn->close();
?>