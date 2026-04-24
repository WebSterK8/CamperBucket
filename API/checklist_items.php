
<?php
require_once 'controlelogin.php';

header('Content-Type: application/json');
require_once '../dbconnect.php';

$categorie = isset($_GET['Category']) ? $_GET['Category'] : null;
$aanbod = isset($_GET['ServiceName']) ? $_GET['ServiceName'] : null;
$prijs = isset($_GET['Price']) ? $_GET['Price'] : null;
$omschrijving = isset($_GET['Description']) ? $_GET['Description'] : null;


// zet identifiers tussen ' ' om parserproblemen te voorkomen, Description stond in het blauw
//$sql = "SELECT 'ServiceID', 'ServiceName', 'Price', 'Description', 'Category'  FROM tblservices";
//$sql = "SELECT * FROM tblservices";

$sql = "SELECT * FROM tbl_items WHERE 1=1";

$params = [];
$types = "";


// Voeg filters toe als waarden beschikbaar zijn
if (!empty($categorie)) {
    $sql .= " AND categorie = ?";
    $params[] = $categorie;
    $types .= "s";
}

if (!empty($aanbod)) {
    $sql .= " AND ServiceName LIKE ?";
    $params[] = "%$aanbod%";
    $types .= "s";
}

// Filter op prijs: behandel ingevoerde prijs als maximale prijs (<=)
if (!empty($prijs) && is_numeric($prijs)) {
    $sql .= " AND Price <= ?";
    $params[] = $prijs;
    // gebruik 'd' (double) voor numerieke waarden
    $types .= "d";
}

echo $sql . "<br><br>";

// Bereid de query voor en voer deze uit
$stmt = $conn->prepare($sql);


// Controleer of er parameters zijn
if (!empty($params)) {
    // Combineer $types en $params in één array
    $bindNames = array_merge([$types], $params);
    // Gebruik call_user_func_array om de parameters correct te binden
    call_user_func_array([$stmt, 'bind_param'], refValues($bindNames));
}


$stmt->execute();
$result = $stmt->get_result();





if (!$result) {
    http_response_code(500); // Interne serverfout
    error_log('Database fout: ' . $conn->error); // Fout loggen
    echo json_encode([
        'status' => 'error',
        'message' => 'Er is een fout opgetreden tijdens het ophalen van gegevens.'
    ]);
    
    exit;
}


$items = [];

if ($result->num_rows > 0) {  

    while($row = $result->fetch_assoc()) { 
        $items[] = $row;
    }
}


echo json_encode($items);
$stmt->close();
$conn->close();


// Functie voor verwijzing naar variabelen (belangrijk voor call_user_func_array)
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        // Creëer een referentie naar elke waarde in de array
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}



?>