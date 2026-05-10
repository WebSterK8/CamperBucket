<?php
require_once 'dbconnect.php';

$gebruikersnaam = 'testuser';    // 5-15 tekens
$wachtwoord     = 'wachtwoord1'; // min. 8 tekens

$hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO tbl_gebruikers (gebruikersnaam, wachtwoord) VALUES (?, ?)");
$stmt->bind_param("ss", $gebruikersnaam, $hash);

echo $stmt->execute() ? "Gebruiker aangemaakt!" : "Fout: " . $conn->error;

$stmt->close();
$conn->close();
