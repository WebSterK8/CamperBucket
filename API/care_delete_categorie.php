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
    $slug = trim($data['slug'] ?? '');

    if (empty($slug)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Slug is verplicht.']);
        exit;
    }

    // blokkeren als de categorie nog taken bevat (verplaats of verwijder ze eerst)
    $telStmt = $conn->prepare("SELECT COUNT(*) AS aantal FROM tbl_care_items WHERE categorie = ?");
    $telStmt->bind_param("s", $slug);
    $telStmt->execute();
    $aantal = $telStmt->get_result()->fetch_assoc()['aantal'];
    $telStmt->close();

    if ($aantal > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Deze categorie bevat nog ' . $aantal . ' taak/taken. Verplaats of verwijder die eerst.'
        ]);
        exit;
    }

    $del = $conn->prepare("DELETE FROM tbl_care_categorie WHERE slug = ?");
    $del->bind_param("s", $slug);

    if ($del->execute()) {

        if ($del->affected_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Categorie niet gevonden.']);
        } else {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'slug' => $slug
            ]);
        }

    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $del->error
        ]);
    }

    $del->close();
    $conn->close();
}
?>
