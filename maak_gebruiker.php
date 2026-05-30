<?php
require_once 'dbconnect.php';

$gebruikersnaam = '';    // 5-15 tekens
$wachtwoord     = ''; // min. 8 tekens

$hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO tbl_gebruikers (gebruikersnaam, wachtwoord) VALUES (?, ?)");
$stmt->bind_param("ss", $gebruikersnaam, $hash);

echo $stmt->execute() ? "Gebruiker aangemaakt!" : "Fout: " . $conn->error;

$stmt->close();
$conn->close();
