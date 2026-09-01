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
    $naam = trim($data['naam'] ?? '');

    // Input validatie: verplichte velden
    if (empty($slug) || empty($naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Slug en naam zijn verplicht.']);
        exit;
    }

    // Input validatie: lengte + regex (letters, spaties, koppeltekens, apostrofs en ampersand)
    if (strlen($naam) > 50 || !preg_match("/^[\p{L}\s\-'&]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens, apostrofs of &.']);
        exit;
    }

    // enkel de zichtbare naam wijzigen; de slug blijft de vaste sleutel
    $sql = "UPDATE tbl_locatie_categorie SET naam = ? WHERE slug = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ss", $naam, $slug);

    if ($stmt->execute()) {

        if ($stmt->affected_rows === 0) {
            // geen rij gewijzigd: slug bestaat niet (of naam ongewijzigd)
            $check = $conn->prepare("SELECT 1 FROM tbl_locatie_categorie WHERE slug = ?");
            $check->bind_param("s", $slug);
            $check->execute();
            $check->store_result();
            $bestaat = $check->num_rows > 0;
            $check->close();

            if (!$bestaat) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Categorie niet gevonden.']);
                $stmt->close();
                $conn->close();
                exit;
            }
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'slug' => $slug,
            'naam' => $naam
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
?>
