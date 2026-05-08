<?php
// login controle voor API-endpoints: geeft 401 JSON terug ipv redirect
if (!isset($_SESSION["ingelogd"]) || $_SESSION["ingelogd"] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd.']);
    exit;
}
?>
