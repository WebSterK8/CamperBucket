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

    // Input opschonen met trim()
    $naam        = trim($data['naam'] ?? '');
    $categorie   = trim($data['categorie'] ?? '');
    $checklistId = isset($data['checklist_id']) ? (int) $data['checklist_id'] : 0;

    // Input validatie: verplichte velden
    if (empty($naam) || empty($categorie) || $checklistId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam, categorie en checklist_id zijn verplicht.']);
        exit;
    }

    // Input validatie: whitelist categorie
    if (!in_array($categorie, ['food', 'stuff'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Categorie moet food of stuff zijn.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (strlen($naam) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens of apostrofs.']);
        exit;
    }

    // Controleren of de checklist bestaat
    $checkStmt = $conn->prepare("SELECT id FROM tbl_checklist WHERE id = ?");
    $checkStmt->bind_param("i", $checklistId);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Checklist bestaat niet.']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();

    // Item toevoegen aan tbl_items, gekoppeld aan checklist_id
    $sql = "INSERT INTO tbl_items (naam, categorie, checklist_id) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $naam, $categorie, $checklistId);

    if ($stmt->execute()) {

        $itemId = $conn->insert_id;

        // Item koppelen aan de checklist in tbl_checklist_items (unchecked)
        $sqlLink = "INSERT INTO tbl_checklist_items (checklist_id, item_id, checked) VALUES (?, ?, 0)";
        $stmtLink = $conn->prepare($sqlLink);
        $stmtLink->bind_param("ii", $checklistId, $itemId);
        $stmtLink->execute();
        $stmtLink->close();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'id' => $itemId,
            'naam' => $naam,
            'categorie' => $categorie
        ]);

    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
}