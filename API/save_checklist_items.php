<?php
//require_once 'controlelogin.php'; // login controle
require_once '../dbconnect.php'; // veilige database connectie

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
    if (!isset($data['checklist_id']) || !is_numeric($data['checklist_id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige checklist_id']); // Veilige JSON output
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $checklist_id = (int)$data['checklist_id'];

    $items = $data['items'] ?? [];

    if (!$checklist_id || !is_array($items)) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige data']);
        exit;
    }

    // 1. oude items verwijderen
    $sqlDelete = "DELETE FROM tbl_checklist_items WHERE checklist_id = ?";

    // Prepared Statements
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $checklist_id);

    if (!$stmtDelete->execute()) {
        http_response_code(500);
        echo json_encode([
            'message' => 'Fout bij verwijderen items',
            'error' => $stmtDelete->error
        ]);
        exit;
    }

    // 2. nieuwe items opslaan
    $sqlInsert = "INSERT INTO tbl_checklist_items (checklist_id, item_id, checked)
                  VALUES (?, ?, ?)";

    $stmtInsert = $conn->prepare($sqlInsert);

    foreach ($items as $item) {

        if (!isset($item['item_id']) || !is_numeric($item['item_id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige item_id']);
        exit;
        }

        $item_id = (int)$item['item_id'];
        $checked = ((int)$item['checked'] === 1) ? 1 : 0; // beperkt tot 0 of 1

        $stmtInsert->bind_param("iii", $checklist_id, $item_id, $checked);

        if (!$stmtInsert->execute()) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Fout bij opslaan item',
                'error' => $stmtInsert->error
            ]);
            exit;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Checklist items opgeslagen'
    ]);

    $stmtDelete->close();
    $stmtInsert->close();
    $conn->close();
}
?>