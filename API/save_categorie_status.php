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

    // Input opschonen
    $categorie = trim($data['categorie'] ?? '');
    $persoon   = trim($data['persoon'] ?? '');
    $checked   = ((int) ($data['checked'] ?? 0) === 1) ? 1 : 0; // beperkt tot 0 of 1

    // Input validatie: categorie moet bestaan in tbl_categorie
    $check = $conn->prepare("SELECT 1 FROM tbl_categorie WHERE slug = ?");
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

    // Input validatie: whitelist persoon (enkel 'kaatje' of 'ben')
    if (!in_array($persoon, ['kaatje', 'ben'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige persoon.']);
        exit;
    }

    // upsert: één rij per (categorie, persoon)
    $sql = "INSERT INTO tbl_categorie_status (categorie, persoon, checked)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE checked = VALUES(checked)";

    $stmt = $conn->prepare($sql); // Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ssi", $categorie, $persoon, $checked);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Status opgeslagen'
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
