<?php

session_start();
$config = require __DIR__ . '/config/app.php'; /* het resultaat, de array van het bestand app.php wordt toegekend aan variabele $config */
$servername = $config['database']["servername"];
$username = $config['database']["username"];
$password = $config['database']["password"];
$dbname = $config['database']["dbname"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname); /* de databaseverbinding $conn is nu een object met eigenschappen */

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

?>