<?php
require_once 'controlelogin.php';
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
    
    $land  = trim($data['land']); 
    $regio = trim($data['regio']);
    $jaar  = trim($data['jaar']);
    $maand_week  = trim($data['mnWk']);

    $titel = $land . ' ' . $jaar;
    

    // Input validatie
    if (empty($land) || empty($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Minstens land en jaar invullen']);
        exit;
    }

    if (!is_numeric($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Het jaar moet numeriek zijn.']);
        exit;
    }

    

    $sql = "UPDATE tbl_checklist 
        SET land=?, regio=?, jaar=?, maand_week=?
        WHERE id=?";

    $stmt = $conn->prepare($sql); // Prepared Statements
    $stmt->bind_param("ssisi", $land, $regio, $jaar, $maand_week, $id);
    
    if ($stmt->execute()) {

      
        //$id = $conn->insert_id; // geen insert bij update

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


?>