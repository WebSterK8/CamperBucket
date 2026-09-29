<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// alle vinkjes van één categorie uitzetten (naam, notitie, toewijzing en volgorde blijven behouden)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    // Input opschonen met trim()
    $slug = trim($data['slug'] ?? '');

    if (empty($slug)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Slug is verplicht.']);
        exit;
    }

    $sql = "UPDATE tbl_care_items SET checked = 0, checked_kaatje = 0, checked_ben = 0 WHERE categorie = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("s", $slug);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'slug' => $slug]); // Veilige JSON output
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
