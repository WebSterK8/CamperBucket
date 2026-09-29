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

    // Input validatie: categorie moet bestaan in tbl_care_categorie
    $check = $conn->prepare("SELECT 1 FROM tbl_care_categorie WHERE slug = ?");
    $check->bind_param("s", $categorie);
    $check->execute();
    $check->store_result();
    $categorieBestaat = $check->num_rows > 0;
    $check->close();

    if (!$categorieBestaat) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige categorie.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend; ruimer dan de checklist: ook cijfers en leestekens)
    if (mb_strlen($naam) > 100 || !preg_match("/^[a-zA-ZÀ-ÿ0-9\s\-'&+\/(),.:!?]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 100 letters, cijfers, spaties of - \' & + / ( ) , . : ! ?']);
        exit;
    }

    // positie achteraan in de categorie (max + 1)
    $posStmt = $conn->prepare("SELECT COALESCE(MAX(positie), 0) + 1 AS pos FROM tbl_care_items WHERE categorie = ?");
    $posStmt->bind_param("s", $categorie);
    $posStmt->execute();
    $positie = (int) $posStmt->get_result()->fetch_assoc()['pos'];
    $posStmt->close();

    // Taak toevoegen aan tbl_care_items
    $sql = "INSERT INTO tbl_care_items (naam, categorie, positie) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ssi", $naam, $categorie, $positie);

    if ($stmt->execute()) {

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id,
            'naam' => $naam,
            'categorie' => $categorie,
            'positie' => $positie
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
