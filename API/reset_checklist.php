<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // alle item-vinkjes uitzetten (toewijzing en optioneel blijven behouden)
    if (!$conn->query("UPDATE tbl_items SET checked = 0")) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $conn->error]); // Veilige JSON output
        exit;
    }

    // alle K/B categorie-toggles uitzetten
    if (!$conn->query("UPDATE tbl_categorie_status SET checked = 0")) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $conn->error]); // Veilige JSON output
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Checklist gereset'
    ]); // Veilige JSON output

    $conn->close();
}
?>
