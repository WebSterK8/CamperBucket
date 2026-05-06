<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
//require_once 'controlelogin.php'; // login controle 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    // Input validatie: verplicht + numeriek
    if (empty($data['id']) || !is_numeric($data['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige ID.']);
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $id = (int) $data['id'];

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige ID.']);
        exit;
    }

    // 1. gekoppelde items verwijderen
    $sqlItems = "DELETE FROM tbl_checklist_items WHERE checklist_id = ?";
    $stmtItems = $conn->prepare($sqlItems); // Prepared Statements
    $stmtItems->bind_param("i", $id);

    if (!$stmtItems->execute()) {
        http_response_code(500);
        echo json_encode([
            'message' => 'Fout bij verwijderen items.',
            'error' => $stmtItems->error
        ]);
        exit;
    }

    // 2. checklist zelf verwijderen
    $sql = "DELETE FROM tbl_checklist WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Checklist succesvol verwijderd.']);
    } else {
        http_response_code(500);
        echo json_encode([
            'message' => 'Fout bij verwijderen checklist.',
            'error' => $stmt->error
        ]);
    }

    $stmtItems->close();
    $stmt->close();
    $conn->close();
}
?>
