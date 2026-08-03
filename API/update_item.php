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

    // Input opschonen met trim()
    $naam = trim($data['naam'] ?? '');

    // Input validatie: verplichte velden
    if (empty($naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam is verplicht.']); // Veilige JSON output
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (strlen($naam) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens of apostrofs.']); // Veilige JSON output
        exit;
    }

    // naam bijwerken
    $sql = "UPDATE tbl_items SET naam = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("si", $naam, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'naam' => $naam
        ]); // Veilige JSON output
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
