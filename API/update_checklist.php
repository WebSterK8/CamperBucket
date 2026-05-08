<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

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
    $id = trim($data['id']); 
    $land  = trim($data['land']); 
    $regio = trim($data['regio']);
    $jaar  = trim($data['jaar']);
    $maand_week  = trim($data['mnWk']);

    

    // Input validatie: verplichte velden
    if (empty($id) || empty($land) || empty($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Minstens id, land en jaar invullen']);
        exit;
    }

    // Input validatie: controle datatype
    if (!is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['message' => 'De ID moet numeriek zijn.']);
        exit;
    }

    if (!is_numeric($jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Het jaar moet numeriek zijn.']);
        exit;
    }

    // Input validatie: lengte + regex (overeenkomstig frontend)
    if (strlen($land) > 50 || !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/", $land)) {
        http_response_code(400);
        echo json_encode(['message' => 'Land: max 50 letters, spaties, koppeltekens of apostrofs']);
        exit;
    }

    if (strlen($regio) > 100 || (strlen($regio) > 0 && !preg_match("/^[a-zA-ZÀ-ÿ\s\-']+$/", $regio))) {
        http_response_code(400);
        echo json_encode(['message' => 'Regio: max 100 letters, spaties, koppeltekens of apostrofs']);
        exit;
    }

    if (strlen($jaar) !== 4 || !preg_match("/^[0-9]{4}$/", $jaar)) {
        http_response_code(400);
        echo json_encode(['message' => 'Jaar: exact 4 cijfers']);
        exit;
    }

    if (strlen($maand_week) > 50 || (strlen($maand_week) > 0 && !preg_match("/^[a-zA-Z0-9\s\-\/]+$/", $maand_week))) {
        http_response_code(400);
        echo json_encode(['message' => 'Maand/week: max 50 tekens (letters, cijfers, spaties, - of /)']);
        exit;
    }

    $id = (int) $id;



    $sql = 
    "UPDATE tbl_checklist 
    SET land=?, regio=?, jaar=?, maand_week=?
    WHERE id=?";
    

    $stmt = $conn->prepare($sql); // Prepared Statements
    $stmt->bind_param("ssisi", $land, $regio, $jaar, $maand_week, $id);
    
    if ($stmt->execute()) {

        http_response_code(200);
        echo json_encode([
            'message' => 'Checklist succesvol bijgewerkt.',
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