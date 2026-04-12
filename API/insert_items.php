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
    
    $aanbod = Inputbeveiliging($data['ServiceName']);
    $prijs = Inputbeveiliging($data['Price']);
    $omschrijving= Inputbeveiliging($data['Description']);
    $categorie = Inputbeveiliging($data['Category']);
    
    

    // Validatie van invoer
    if (empty($aanbod) || empty($prijs) || empty($omschrijving) || empty($categorie)) {
        http_response_code(400);
        echo json_encode(['message' => 'Alle velden moeten worden ingevuld.']);
        exit;
    }
    
    if (!is_numeric($prijs)) {
        http_response_code(400);
        echo json_encode(['message' => 'De prijs moet numeriek zijn.']);
        exit;
    }
    
    $sql = "INSERT INTO `tblservices` (`ServiceName`, `Price`, `Description`, `Category`)
    VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $aanbod, $prijs, $omschrijving, $categorie);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Dienst succesvol toegevoegd.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Fout bij het invoegen in de database.', 'error' => $stmt->error]);
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
