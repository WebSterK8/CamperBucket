<?php
require_once 'controlelogin.php';
require_once '../dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON']);
        exit;
    }

    $checklist_id = (int)$data['checklist_id'];
    $items = $data['items'];

    if (!$checklist_id || !is_array($items)) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige data']);
        exit;
    }

    // 1. oude items verwijderen
    $sqlDelete = "DELETE FROM tbl_checklist_items WHERE checklist_id = ?";
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

        $item_id = (int)$item['item_id'];
        $checked = (int)$item['checked'];

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