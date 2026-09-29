<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    // Input validatie: verplicht + numeriek
    if (empty($data['id']) || !is_numeric($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige id']); // Veilige JSON output
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $id = (int) $data['id'];
    $checked       = ((int) ($data['checked'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1
    $checkedKaatje = ((int) ($data['checked_kaatje'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1
    $checkedBen    = ((int) ($data['checked_ben'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1

    // enkel de vinkjes bijwerken (naam, notitie en toewijzing gaan via care_update_item.php)
    $sql = "UPDATE tbl_care_items SET checked = ?, checked_kaatje = ?, checked_ben = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("iiii", $checked, $checkedKaatje, $checkedBen, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]); // Veilige JSON output
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $stmt->error
        ]); // Veilige JSON output
    }

    $stmt->close();
    $conn->close();
}
?>
