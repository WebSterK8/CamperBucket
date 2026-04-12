<?php
header('Content-Type: application/json');

// Enkel POST toelaten
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Alleen POST toegestaan"]);
    exit;
}

// JSON input lezen
$input = file_get_contents("php://input");
$data  = json_decode($input, true);

// Controle op input
if (!$data || !isset($data['lat1'], $data['lng1'], $data['lat2'], $data['lng2'])) {
    http_response_code(400);
    echo json_encode(["error" => "Ongeldige input"]);
    exit;
}

// API key
include '../config/config.php';

// Coördinaten
$lat1 = (float)$data['lat1'];
$lng1 = (float)$data['lng1'];
$lat2 = (float)$data['lat2'];
$lng2 = (float)$data['lng2'];

// Waypoints samenstellen
$waypoints = "$lat1,$lng1|$lat2,$lng2";

// Transportmodi
$modes = ['drive', 'walk'];

// Route zoeken
foreach ($modes as $mode) {

    $url = "https://api.geoapify.com/v1/routing?"
    . "waypoints=" . urlencode($waypoints)
    . "&mode=$mode"
    . "&apiKey=$API_KEY";

    // API oproep
    $response = file_get_contents($url);

    // JSON omzetten naar array
    $json = json_decode($response, true);

    // Controle of route bestaat
    if ($json && isset($json['features'][0])) {

        $route = $json['features'][0];

        $afstandKm = $route['properties']['distance'] / 1000;
        $tijdMin   = round($route['properties']['time'] / 60);

        echo json_encode([
            "afstand"  => round($afstandKm, 2),
            "tijd"     => $tijdMin,
            "geometry" => $route['geometry']
        ]);

        exit;
    }
}

// Geen route gevonden
http_response_code(404);
echo json_encode([
    "error" => "Geen route gevonden tussen de geselecteerde punten"
]);
