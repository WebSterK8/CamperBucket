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
    $geldigeCategorieen = [
        'persoonlijke_verzorging',
        'kledij',
        'slaapgerief',
        'kampeergerief',
        'keuken_huishouden',
        'eten_drinken',
        'elektronica_administratie',
    ];

    if (!in_array($categorie, $geldigeCategorieen, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige categorie.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (strlen($naam) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens of apostrofs.']);
        exit;
    }

    // Item toevoegen aan tbl_items
    $sql = "INSERT INTO tbl_items (naam, categorie) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $naam, $categorie);

    if ($stmt->execute()) {

        $itemId = $conn->insert_id;

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