<?php
require_once '../dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON-gegevens.']);
        exit;
    }
    
    $land  = Inputbeveiliging($data['land']); // ? BETER trim($data['land'] ?? '');   ?
    $regio = Inputbeveiliging($data['regio']);
    $jaar  = Inputbeveiliging($data['jaar']);
    $maand_week  = Inputbeveiliging($data['mnWk']);

    $titel = $land . ' ' . $jaar;

    // Validatie invoer
    if (empty($land) || empty($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Minstens land en jaar invullen']);
        exit;
    }

    $sql = "INSERT INTO tbl_checklist (land, regio, jaar, maand_week, titel)
    VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $land, $regio, $jaar, $maand_week, $titel);
    
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


function Inputbeveiliging($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>