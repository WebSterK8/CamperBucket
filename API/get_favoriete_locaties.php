<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// favoriete locaties over alle reizen heen, met reisnaam/datum erbij zodat de context duidelijk blijft
$sql = "SELECT l.id, l.reis_id, l.naam, l.beschrijving, l.categorie, l.lat, l.lon, l.link, l.foto, l.favoriet,
               r.land AS reis_land, r.start_jaar, r.start_maand, r.eind_jaar
        FROM tbl_locaties l
        JOIN tbl_reizen r ON r.id = l.reis_id
        WHERE l.favoriet = 1
        ORDER BY l.id DESC";

$result = $conn->query($sql);

$locaties = [];

while ($row = $result->fetch_assoc()) {
    $row['lat'] = (float) $row['lat'];
    $row['lon'] = (float) $row['lon'];
    $row['favoriet'] = (bool) $row['favoriet'];
    $locaties[] = $row;
}

echo json_encode($locaties); // Veilige JSON output

$conn->close();
?>
