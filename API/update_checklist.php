<?php
require_once '../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['message' => 'Ongeldige JSON-gegevens.']);
        exit;
    }
    
    $land  = Inputbeveiliging($data['land']);
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

    $sql = "UPDATE tbl_checklist 
        SET land=?, regio=?, jaar=?, maand_week=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $land, $regio, $jaar, $maand_week, $id);
    
    if ($stmt->execute()) {

       // $id = $stmt->insert_id; //  BELANGRIJK
        $id = $conn->insert_id; //  BELANGRIJK 

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
    $conn->close();
}


function Inputbeveiliging($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>