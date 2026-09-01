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
    $beschrijving = trim($data['beschrijving'] ?? '');
    $categorie = trim($data['categorie'] ?? '');
    $link = trim($data['link'] ?? '');

    // Input validatie: verplichte velden
    if (empty($naam) || empty($categorie)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam en categorie zijn verplicht.']); // Veilige JSON output
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend, zelfde stijl als 'land' bij reizen)
    if (strlen($naam) > 100 || !preg_match("/^[\p{L}0-9\s\-',.]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 100 tekens (letters, cijfers, spaties, koppeltekens, komma\'s of punten).']); // Veilige JSON output
        exit;
    }

    // Input validatie: beschrijving optioneel, max lengte
    if (strlen($beschrijving) > 1000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Beschrijving: max 1000 tekens.']); // Veilige JSON output
        exit;
    }
    $beschrijving = $beschrijving === '' ? null : $beschrijving;

    // Input validatie: categorie moet bestaan in tbl_locatie_categorie
    $check = $conn->prepare("SELECT 1 FROM tbl_locatie_categorie WHERE slug = ?");
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

    // Input validatie: lat/lon verplicht en numeriek binnen geldig bereik
    if (!isset($data['lat']) || !is_numeric($data['lat']) || !isset($data['lon']) || !is_numeric($data['lon'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige coördinaten.']); // Veilige JSON output
        exit;
    }

    $lat = (float) $data['lat'];
    $lon = (float) $data['lon'];

    if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Coördinaten buiten bereik.']); // Veilige JSON output
        exit;
    }

    // Input validatie: link optioneel, maar indien ingevuld moet het een geldige URL zijn
    if ($link !== '') {
        if (strlen($link) > 500 || !filter_var($link, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Link: ongeldige URL (max 500 tekens).']); // Veilige JSON output
            exit;
        }
    } else {
        $link = null;
    }

    $sql = "UPDATE tbl_locaties SET naam = ?, beschrijving = ?, categorie = ?, lat = ?, lon = ?, link = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("sssddsi", $naam, $beschrijving, $categorie, $lat, $lon, $link, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'naam' => $naam,
            'beschrijving' => $beschrijving,
            'categorie' => $categorie,
            'lat' => $lat,
            'lon' => $lon,
            'link' => $link
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
