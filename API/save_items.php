<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    // Input validatie
    $items = $data['items'] ?? [];

    if (!is_array($items)) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige data']); // Veilige JSON output
        exit;
    }

    // per item: checked, toegewezen en optioneel bijwerken
    $sql = "UPDATE tbl_items SET checked = ?, toegewezen = ?, optioneel = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie

    foreach ($items as $item) {

        // Input validatie: verplicht + numeriek
        if (!isset($item['id']) || !is_numeric($item['id'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Ongeldige id']); // Veilige JSON output
            exit;
        }

        // Input opschonen met (int) - altijd een getal
        $id = (int) $item['id'];
        $checked = ((int) ($item['checked'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1
        $optioneel = ((int) ($item['optioneel'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1

        // Input validatie: whitelist toegewezen (enkel 'kaatje', 'ben' of null toegelaten)
        $toegewezen = $item['toegewezen'] ?? null;
        if (!in_array($toegewezen, ['kaatje', 'ben'], true)) {
            $toegewezen = null;
        }

        $stmt->bind_param("isii", $checked, $toegewezen, $optioneel, $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Fout bij opslaan item',
                'error' => $stmt->error
            ]); // Veilige JSON output
            exit;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Items opgeslagen'
    ]); // Veilige JSON output

    $stmt->close();
    $conn->close();
}
?>
