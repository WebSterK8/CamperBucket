<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json; charset=utf-8');

// ======= Zet je Geoapify API key hier =======
include '../config/config.php';

function fail($msg, $code = 400) {
  http_response_code($code);
  echo json_encode(["error" => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}

$action = $_GET["action"] ?? "";
if ($action === "") fail("Missing action");

$geoUrl = "";

// ======= 1) Campings zoeken rond lat/lon =======
if ($action === "campings") {
  $lat = $_GET["lat"] ?? "";
  $lon = $_GET["lon"] ?? "";
  $radius = intval($_GET["radius"] ?? 10000);
  $limit = intval($_GET["limit"] ?? 50);

  if ($lat === "" || $lon === "") fail("Missing lat/lon");

  $lat = floatval($lat);
  $lon = floatval($lon);

  $params = http_build_query([
    "categories" => "camping",
    "filter" => "circle:$lon,$lat,$radius",   // let op: lon,lat
    "bias" => "proximity:$lon,$lat",
    "limit" => strval($limit),
    "apiKey" => $API_KEY
  ]);

  $geoUrl = "https://api.geoapify.com/v2/places?$params";
}

// ======= 2) Route berekenen start -> bestemming =======
else if ($action === "route") {
  $fromLat = $_GET["fromLat"] ?? "";
  $fromLon = $_GET["fromLon"] ?? "";
  $toLat   = $_GET["toLat"] ?? "";
  $toLon   = $_GET["toLon"] ?? "";
  $mode    = $_GET["mode"] ?? "drive";

  if ($fromLat === "" || $fromLon === "" || $toLat === "" || $toLon === "") {
    fail("Missing coordinates");
  }

  $fromLat = floatval($fromLat);
  $fromLon = floatval($fromLon);
  $toLat   = floatval($toLat);
  $toLon   = floatval($toLon);

  $params = http_build_query([
    "waypoints" => $fromLat . "," . $fromLon . "|" . $toLat . "," . $toLon,
    "mode" => $mode,
    "apiKey" => $API_KEY
  ]);

  $geoUrl = "https://api.geoapify.com/v1/routing?$params";
}

else {
  fail("Unknown action. Use action=campings|route");
}

// ======= cURL request naar Geoapify =======
$ch = curl_init($geoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
  $err = curl_error($ch);
  curl_close($ch);
  fail("cURL error: " . $err, 500);
}

curl_close($ch);

// geef Geoapify response door
http_response_code($httpCode);
echo $response;
