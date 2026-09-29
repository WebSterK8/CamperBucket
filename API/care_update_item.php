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
    $naam    = trim($data['naam'] ?? '');
    $notitie = trim($data['notitie'] ?? '');

    // Input validatie: verplichte velden
    if (empty($naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam is verplicht.']); // Veilige JSON output
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (mb_strlen($naam) > 100 || !preg_match("/^[a-zA-ZÀ-ÿ0-9\s\-'&+\/(),.:!?]+$/u", $naam)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Naam: max 100 letters, cijfers, spaties of - \' & + / ( ) , . : ! ?']); // Veilige JSON output
        exit;
    }

    // notitie is vrije tekst (wordt in de frontend veilig via textContent getoond), enkel lengte beperken
    if (mb_strlen($notitie) > 255) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Notitie: max 255 tekens.']); // Veilige JSON output
        exit;
    }
    $notitie = $notitie === '' ? null : $notitie; // lege notitie als NULL bewaren

    // Input validatie: whitelist toegewezen (enkel 'kaatje', 'ben', 'allebei' of null toegelaten)
    $toegewezen = $data['toegewezen'] ?? null;
    if (!in_array($toegewezen, ['kaatje', 'ben', 'allebei'], true)) {
        $toegewezen = null;
    }

    // huidige categorie opzoeken (om te weten of de taak verplaatst wordt)
    $huidig = $conn->prepare("SELECT categorie, positie FROM tbl_care_items WHERE id = ?");
    $huidig->bind_param("i", $id);
    $huidig->execute();
    $rij = $huidig->get_result()->fetch_assoc();
    $huidig->close();

    if (!$rij) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Taak niet gevonden.']); // Veilige JSON output
        exit;
    }

    $categorie = trim($data['categorie'] ?? '') ?: $rij['categorie'];
    $positie   = (int) $rij['positie'];

    if ($categorie !== $rij['categorie']) {
        // categorie moet bestaan in tbl_care_categorie
        $check = $conn->prepare("SELECT 1 FROM tbl_care_categorie WHERE slug = ?");
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

        // verplaatste taak komt achteraan in de nieuwe categorie
        $posStmt = $conn->prepare("SELECT COALESCE(MAX(positie), 0) + 1 AS pos FROM tbl_care_items WHERE categorie = ?");
        $posStmt->bind_param("s", $categorie);
        $posStmt->execute();
        $positie = (int) $posStmt->get_result()->fetch_assoc()['pos'];
        $posStmt->close();
    }

    $sql = "UPDATE tbl_care_items SET naam = ?, notitie = ?, toegewezen = ?, categorie = ?, positie = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ssssii", $naam, $notitie, $toegewezen, $categorie, $positie, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'naam' => $naam,
            'notitie' => $notitie,
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
