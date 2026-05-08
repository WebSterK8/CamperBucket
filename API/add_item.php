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
    $naam      = trim($data['naam'] ?? '');
    $categorie = trim($data['categorie'] ?? '');

    // Input validatie: verplichte velden
    if (empty($naam) || empty($categorie)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam en categorie zijn verplicht.']);
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

    $sql = "INSERT INTO tbl_items (naam, categorie) VALUES (?, ?)";

    // Prepared Statements
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $naam, $categorie);

    if ($stmt->execute()) {

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id,
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