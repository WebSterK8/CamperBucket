<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// nieuwe volgorde opslaan na slepen: per categorie de taak-id's in hun nieuwe volgorde
// verwacht { "lijsten": { "slug": [id, id, ...], ... } } (één lijst, of twee bij slepen naar een andere categorie)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['lijsten']) || !is_array($data['lijsten'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }

    $check = $conn->prepare("SELECT 1 FROM tbl_care_categorie WHERE slug = ?");
    $upd = $conn->prepare("UPDATE tbl_care_items SET categorie = ?, positie = ? WHERE id = ?"); // Prepared Statements, tegen SQL injectie

    $conn->begin_transaction(); // alles of niets

    foreach ($data['lijsten'] as $slug => $ids) {

        // Input validatie: categorie moet bestaan in tbl_care_categorie
        $slug = (string) $slug;
        $check->bind_param("s", $slug);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0 || !is_array($ids)) {
            $conn->rollback();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ongeldige categorie.']); // Veilige JSON output
            exit;
        }

        // posities opnieuw nummeren (1, 2, 3, ...) in de meegegeven volgorde
        foreach (array_values($ids) as $i => $id) {

            // Input validatie: numeriek
            if (!is_numeric($id)) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Ongeldige id']); // Veilige JSON output
                exit;
            }

            $id = (int) $id;
            $positie = $i + 1;
            $upd->bind_param("sii", $slug, $positie, $id);

            if (!$upd->execute()) {
                $conn->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $upd->error]); // Veilige JSON output
                exit;
            }
        }
    }

    $conn->commit();

    echo json_encode(['success' => true]); // Veilige JSON output

    $check->close();
    $upd->close();
    $conn->close();
}
?>
