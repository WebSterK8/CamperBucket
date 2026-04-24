<?php
require_once 'controlelogin.php';
require_once '../dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || empty($data['naam']) || empty($data['categorie'])) {
        echo json_encode(['success' => false, 'message' => 'Ongeldige input']);
        exit;
    }

    $naam = trim($data['naam']);
    $categorie = trim($data['categorie']);

    $sql = "INSERT INTO tbl_items (naam, categorie) VALUES (?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $naam, $categorie);

    if ($stmt->execute()) {

        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id,
            'naam' => $naam,
            'categorie' => $categorie
        ]);

    } else {
        echo json_encode([
            'success' => false,
            'error' => $stmt->error
        ]);
    }

    $stmt->close();
    $conn->close();
}