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
        echo json_encode(['success' => false, 'message' => 'Ongeldige ID.']);
        exit;
    }

    if (!isset($data['favoriet']) || !is_bool($data['favoriet'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige favoriet-waarde.']);
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $id = (int) $data['id'];
    $favoriet = $data['favoriet'] ? 1 : 0;

    $sql = "UPDATE tbl_locaties SET favoriet = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ii", $favoriet, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['success' => true, 'favoriet' => (bool) $favoriet]);
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
?>
