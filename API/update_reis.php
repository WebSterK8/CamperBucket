<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST)) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige gegevens.']); // Veilige JSON output
        exit;
    }

    // Input validatie: verplicht + numeriek
    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige ID.']);
        exit;
    }

    // Input opschonen met trim()
    $id = (int) $_POST['id'];
    $land = trim($_POST['land']         ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $foto_alt = trim($_POST['foto_alt']     ?? '');
    $start_jaar = trim($_POST['start_jaar']   ?? '');
    $start_maand = trim($_POST['start_maand']  ?? '');
    $start_dag = trim($_POST['start_dag']    ?? '');
    $eind_jaar = trim($_POST['eind_jaar']    ?? '');
    $eind_maand = trim($_POST['eind_maand']   ?? '');
    $eind_dag = trim($_POST['eind_dag']     ?? '');

    // Foto: nieuw bestand uploaden of bestaand pad bewaren
    $foto = null;
    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $toegestaneTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $toegestaneTypes)) {
            http_response_code(400);
            echo json_encode(['message' => 'Alleen jpg, png, gif of webp toegestaan.']);
            exit;
        }
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['message' => 'Foto mag maximaal 5 MB zijn.']);
            exit;
        }
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $bestandsnaam = uniqid('reis_') . '.' . $ext;
        $uploadPad = '../Afbeeldingen/uploads/' . $bestandsnaam;
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPad)) {
            http_response_code(500);
            echo json_encode(['message' => 'Foto kon niet worden opgeslagen.']);
            exit;
        }
        $foto = 'Afbeeldingen/uploads/' . $bestandsnaam;
    } elseif (!empty($_POST['foto_bestaand'])) {
        $foto = trim($_POST['foto_bestaand']);
    }

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige ID.']);
        exit;
    }

    // Input validatie: verplichte velden
    if (empty($land)) {
        http_response_code(400);
        echo json_encode(['message' => 'Land/bestemming is verplicht.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (strlen($land) > 100 || !preg_match("/^[a-zA-ZÀ-ÿ0-9\s\-',\.]+$/u", $land)) {
        http_response_code(400);
        echo json_encode(['message' => 'Land: max 100 tekens (letters, cijfers, spaties, koppeltekens, komma\'s of punten).']);
        exit;
    }

    if (strlen($beschrijving) > 500) {
        http_response_code(400);
        echo json_encode(['message' => 'Beschrijving: max 500 tekens.']);
        exit;
    }

    if (strlen($foto) > 500) {
        http_response_code(400);
        echo json_encode(['message' => 'Foto: max 500 tekens.']);
        exit;
    }

    if (strlen($foto_alt) > 255) {
        http_response_code(400);
        echo json_encode(['message' => 'Foto alt-tekst: max 255 tekens.']);
        exit;
    }

    // Input validatie: jaartallen numeriek en 4 cijfers
    if (!empty($start_jaar) && (strlen($start_jaar) !== 4 || !preg_match("/^[0-9]{4}$/", $start_jaar))) {
        http_response_code(400);
        echo json_encode(['message' => 'Startjaar: exact 4 cijfers.']);
        exit;
    }

    if (!empty($eind_jaar) && (strlen($eind_jaar) !== 4 || !preg_match("/^[0-9]{4}$/", $eind_jaar))) {
        http_response_code(400);
        echo json_encode(['message' => 'Eindjaar: exact 4 cijfers.']);
        exit;
    }

    // Input validatie: maand (1-12) en dag (1-31)
    if (!empty($start_maand) && (!is_numeric($start_maand) || $start_maand < 1 || $start_maand > 12)) {
        http_response_code(400);
        echo json_encode(['message' => 'Startmaand: getal tussen 1 en 12.']);
        exit;
    }

    if (!empty($start_dag) && (!is_numeric($start_dag) || $start_dag < 1 || $start_dag > 31)) {
        http_response_code(400);
        echo json_encode(['message' => 'Startdag: getal tussen 1 en 31.']);
        exit;
    }

    if (!empty($eind_maand) && (!is_numeric($eind_maand) || $eind_maand < 1 || $eind_maand > 12)) {
        http_response_code(400);
        echo json_encode(['message' => 'Eindmaand: getal tussen 1 en 12.']);
        exit;
    }

    if (!empty($eind_dag) && (!is_numeric($eind_dag) || $eind_dag < 1 || $eind_dag > 31)) {
        http_response_code(400);
        echo json_encode(['message' => 'Einddag: getal tussen 1 en 31.']);
        exit;
    }

    // lege strings omzetten naar NULL voor optionele velden
    $start_jaar = !empty($start_jaar) ? (int) $start_jaar : null;
    $start_maand = !empty($start_maand) ? (int) $start_maand : null;
    $start_dag = !empty($start_dag) ? (int) $start_dag : null;
    $eind_jaar = !empty($eind_jaar) ? (int) $eind_jaar : null;
    $eind_maand = !empty($eind_maand) ? (int) $eind_maand : null;
    $eind_dag = !empty($eind_dag) ? (int) $eind_dag : null;
    $beschrijving = !empty($beschrijving) ? $beschrijving : null;
    $foto = !empty($foto) ? $foto : null;
    $foto_alt = !empty($foto_alt) ? $foto_alt : null;

    $sql =
    "UPDATE tbl_reizen
     SET land=?, beschrijving=?, foto=?, foto_alt=?,
         start_jaar=?, start_maand=?, start_dag=?,
         eind_jaar=?, eind_maand=?, eind_dag=?
     WHERE id=?";

    $stmt = $conn->prepare($sql); // Prepared Statements
    $stmt->bind_param("ssssiiiiiii",
        $land, $beschrijving, $foto, $foto_alt,
        $start_jaar, $start_maand, $start_dag,
        $eind_jaar, $eind_maand, $eind_dag,
        $id
    );

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'message' => 'Reis succesvol bijgewerkt.',
            'id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'message' => 'Fout bij het bijwerken in de database.',
            'error' => $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
}
?>
