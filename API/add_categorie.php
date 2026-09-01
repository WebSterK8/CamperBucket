<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');


// maakt een veilige, unieke slug (interne sleutel) uit een categorie-naam
function maakSlug($naam, $conn) {

    // accenten en bijzondere tekens omzetten naar een simpele ascii-basis
    $slug = mb_strtolower($naam, 'UTF-8');
    $slug = str_replace('&', ' en ', $slug); // ampersand -> "en"

    // veelvoorkomende accenten (À-ÿ) platslaan
    $van  = ['à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'];
    $naar = ['a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y'];
    $slug = str_replace($van, $naar, $slug);

    // al wat geen letter of cijfer is -> underscore, randen opschonen
    $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
    $slug = trim($slug, '_');

    if ($slug === '') {
        $slug = 'categorie'; // fallback wanneer de naam geen ascii oplevert
    }

    $slug = substr($slug, 0, 50);

    // uniek maken: _2, _3, ... indien de slug al bestaat
    $basis = $slug;
    $nr = 2;
    $check = $conn->prepare("SELECT 1 FROM tbl_categorie WHERE slug = ?");
    while (true) {
        $check->bind_param("s", $slug);
        $check->execute();
        $check->store_result();
        $bestaat = $check->num_rows > 0;
        if (!$bestaat) break;
        $slug = substr($basis, 0, 47) . '_' . $nr;
        $nr++;
    }
    $check->close();

    return $slug;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    // Input opschonen met trim()
    $naam = trim($data['naam'] ?? '');

    if (empty($naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam is verplicht.']);
        exit;
    }

    // Input validatie: lengte + regex (letters, spaties, koppeltekens, apostrofs en ampersand)
    if (strlen($naam) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-'&+\/()]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 50 letters, spaties, koppeltekens, apostrofs, &, +, / of ().']);
        exit;
    }

    $slug = maakSlug($naam, $conn);

    // positie achteraan (max + 1)
    $positie = (int) ($conn->query("SELECT COALESCE(MAX(positie), 0) + 1 AS pos FROM tbl_categorie")->fetch_assoc()['pos']);

    $sql = "INSERT INTO tbl_categorie (slug, naam, positie) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ssi", $slug, $naam, $positie);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'slug' => $slug,
            'naam' => $naam,
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
