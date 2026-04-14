<?php
require_once '../dbconnect.php';

header('Content-Type: application/json');

$sql = "SELECT id, titel, land, jaar 
        FROM tbl_checklist 
        ORDER BY id DESC";

$result = $conn->query($sql);

$checklists = [];

while ($row = $result->fetch_assoc()) {
    $checklists[] = $row;
}

echo json_encode($checklists);

$conn->close();
?>