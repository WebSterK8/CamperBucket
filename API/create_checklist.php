<?php
require_once 'controlelogin.php'; // login controle
require_once '../dbconnect.php'; // veilige database connectie

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true); 
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON-gegevens.']); // Veilige JSON output
        exit;
    }
    
    // Input opschonen met trim()
    $land  = trim($data['land']);
    $regio = trim($data['regio']);
    $jaar  = trim($data['jaar']);
    $maand_week  = trim($data['mnWk']);

    $titel = $land . ' ' . $jaar;

    // Input validatie: verplichte velden
    if (empty($land) || empty($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Minstens land en jaar invullen']); 
        exit;
    }

    // Input validatie: controle datatype
    if (!is_numeric($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Het jaar moet numeriek zijn.']);
        exit;
    }

    
    $sql = "INSERT INTO tbl_checklist (land, regio, jaar, maand_week, titel)
    VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql); // query voorbereiden met Prepared Statements, tegen SQL injectie
    $stmt->bind_param("ssiss", $land, $regio, $jaar, $maand_week, $titel);
    
    if ($stmt->execute()) {

        // INSERT CHECKLIST
        $id = $conn->insert_id;

        // INSERT ITEMS (koppelen aan nieuwe checklist)
        $sqlItems = "INSERT INTO tbl_checklist_items (checklist_id, item_id, checked)
        SELECT ?, id, 0 FROM tbl_items";

        $stmtItems = $conn->prepare($sqlItems);
        $stmtItems->bind_param("i", $id);


        if (!$stmtItems->execute()) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Fout bij koppelen items',
                'error' => $stmtItems->error
            ]);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'message' => 'Checklist succesvol toegevoegd.',
            'id' => $id
        ]);

    } else {

        http_response_code(500);
        echo json_encode([
            'message' => 'Fout bij het invoegen in de database.',
            'error' => $stmt->error
        ]);
    }

    $stmt->close();
    $stmtItems->close();
    $conn->close();
}


?>