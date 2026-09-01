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
    if (strlen($naam) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-'&+\/()]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens, apostrofs, &, +, / of ().']); // Veilige JSON output
        exit;
    }

    // Input opschonen: beperkt tot 0 of 1
    $optioneel = ((int) ($data['optioneel'] ?? 0) === 1) ? 1 : 0;

    // Input validatie: whitelist toegewezen (enkel 'kaatje', 'ben', 'allebei' of null toegelaten)
    $toegewezen = $data['toegewezen'] ?? null;
    if (!in_array($toegewezen, ['kaatje', 'ben', 'allebei'], true)) {
        $toegewezen = null;
    }

    // Optioneel: item verplaatsen naar een andere categorie (enkel als 'categorie' is meegegeven)
    $categorie = isset($data['categorie']) ? trim($data['categorie']) : '';

    if ($categorie !== '') {
        // categorie moet bestaan in tbl_categorie
        $check = $conn->prepare("SELECT 1 FROM tbl_categorie WHERE slug = ?");
        $check->bind_param("s", $categorie);
        $check->execute();
        $check->store_result();
        $categorieBestaat = $check->num_rows > 0;
        $check->close();

        if (!$categorieBestaat) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ongeldige categorie.']); // Veilige JSON output
            exit;
        }

        // naam, toewijzing, optioneel én categorie bijwerken
        $sql = "UPDATE tbl_items SET naam = ?, toegewezen = ?, optioneel = ?, categorie = ? WHERE id = ?";
        $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
        $stmt->bind_param("ssisi", $naam, $toegewezen, $optioneel, $categorie, $id);
    } else {
        // naam, toewijzing en optioneel bijwerken (categorie ongewijzigd)
        $sql = "UPDATE tbl_items SET naam = ?, toegewezen = ?, optioneel = ? WHERE id = ?";
        $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
        $stmt->bind_param("ssii", $naam, $toegewezen, $optioneel, $id);
    }

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'naam' => $naam,
            'categorie' => $categorie
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
