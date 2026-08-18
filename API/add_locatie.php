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
    if (empty($data['reis_id']) || !is_numeric($data['reis_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige reis.']);
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $reisId = (int) $data['reis_id'];

    // reis moet bestaan
    $check = $conn->prepare("SELECT 1 FROM tbl_reizen WHERE id = ?");
    $check->bind_param("i", $reisId);
    $check->execute();
    $check->store_result();
    $reisBestaat = $check->num_rows > 0;
    $check->close();

    if (!$reisBestaat) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige reis.']);
        exit;
    }

    // Input opschonen met trim()
    $naam = trim($data['naam'] ?? '');
    $categorie = trim($data['categorie'] ?? '');
    $link = trim($data['link'] ?? '');

    // Input validatie: verplichte velden
    if (empty($naam) || empty($categorie)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam en categorie zijn verplicht.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend, zelfde stijl als 'land' bij reizen)
    if (strlen($naam) > 100 || !preg_match("/^[a-zA-ZÀ-ÿ0-9\s\-',\.]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 100 tekens (letters, cijfers, spaties, koppeltekens, komma\'s of punten).']);
        exit;
    }

    // Input validatie: categorie moet bestaan in tbl_locatie_categorie
    $check = $conn->prepare("SELECT 1 FROM tbl_locatie_categorie WHERE slug = ?");
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

    // Input validatie: lat/lon verplicht en numeriek binnen geldig bereik
    if (!isset($data['lat']) || !is_numeric($data['lat']) || !isset($data['lon']) || !is_numeric($data['lon'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige coördinaten.']);
        exit;
    }

    $lat = (float) $data['lat'];
    $lon = (float) $data['lon'];

    if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Coördinaten buiten bereik.']);
        exit;
    }

    // Input validatie: link optioneel, maar indien ingevuld moet het een geldige URL zijn
    if ($link !== '') {
        if (strlen($link) > 500 || !filter_var($link, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Link: ongeldige URL (max 500 tekens).']);
            exit;
        }
    } else {
        $link = null;
    }

    $sql = "INSERT INTO tbl_locaties (reis_id, naam, categorie, lat, lon, link) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("issdds", $reisId, $naam, $categorie, $lat, $lon, $link);

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'id' => $id,
            'reis_id' => $reisId,
            'naam' => $naam,
            'categorie' => $categorie,
            'lat' => $lat,
            'lon' => $lon,
            'link' => $link
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
