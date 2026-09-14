<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige gegevens.']); // Veilige JSON output
        exit;
    }

    // Input validatie: verplicht + numeriek
    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige id']); // Veilige JSON output
        exit;
    }

    // Input opschonen met (int) - altijd een getal
    $id = (int) $_POST['id'];

    // Input opschonen met trim()
    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $link = trim($_POST['link'] ?? '');

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
    if (!isset($_POST['lat']) || !is_numeric($_POST['lat']) || !isset($_POST['lon']) || !is_numeric($_POST['lon'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige coördinaten.']); // Veilige JSON output
        exit;
    }

    $lat = (float) $_POST['lat'];
    $lon = (float) $_POST['lon'];

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

    // Foto: nieuw bestand uploaden of bestaand pad bewaren
    $foto = null;

    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $toegestaneTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $toegestaneTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Alleen jpg, png, gif of webp toegestaan.']);
            exit;
        }
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Foto mag maximaal 5 MB zijn.']);
            exit;
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $bestandsnaam = uniqid('locatie_') . '.' . $ext;
        $uploadPad = '../Afbeeldingen/uploads/' . $bestandsnaam;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPad)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Foto kon niet worden opgeslagen.']);
            exit;
        }

        $foto = 'Afbeeldingen/uploads/' . $bestandsnaam;

    } elseif (!empty($_POST['foto_bestaand'])) {
        $foto = trim($_POST['foto_bestaand']);
    }

    if ($foto !== null && strlen($foto) > 500) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Foto: max 500 tekens.']);
        exit;
    }

    // oude foto van schijf verwijderen indien vervangen of verwijderd
    $fotoStmt = $conn->prepare("SELECT foto FROM tbl_locaties WHERE id = ?");
    $fotoStmt->bind_param("i", $id);
    $fotoStmt->execute();
    $fotoRij = $fotoStmt->get_result()->fetch_assoc();
    $fotoStmt->close();

    if ($fotoRij && !empty($fotoRij['foto']) && $fotoRij['foto'] !== $foto
        && strpos($fotoRij['foto'], 'Afbeeldingen/uploads/') === 0) {
        $bestandsPad = '../' . $fotoRij['foto'];
        if (file_exists($bestandsPad)) {
            unlink($bestandsPad);
        }
    }

    $sql = "UPDATE tbl_locaties SET naam = ?, beschrijving = ?, categorie = ?, lat = ?, lon = ?, link = ?, foto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("sssddssi", $naam, $beschrijving, $categorie, $lat, $lon, $link, $foto, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'naam' => $naam,
            'beschrijving' => $beschrijving,
            'categorie' => $categorie,
            'lat' => $lat,
            'lon' => $lon,
            'link' => $link,
            'foto' => $foto
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
