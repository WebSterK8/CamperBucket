<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// enkel deze hosts mogen server-side opgehaald worden (voorkomt misbruik van dit endpoint als open url-fetcher)
function bepaalLinkType($host) {
    $host = strtolower($host);

    if ($host === 'goo.gl' || $host === 'maps.app.goo.gl' || preg_match('/(^|\.)google\.[a-z.]{2,6}$/', $host)) {
        return 'google';
    }

    if ($host === 'park4night.com' || preg_match('/(^|\.)park4night\.com$/', $host)) {
        return 'park4night';
    }

    return null;
}


// haalt een url op (met redirects), beperkt tot http/https, met timeout
function haalUrlOp($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
    ]);

    $body = curl_exec($ch);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $fout = curl_errno($ch) ? curl_error($ch) : null;
    curl_close($ch);

    if ($fout || $body === false || $httpCode >= 400) {
        return null;
    }

    return ['body' => $body, 'effectiveUrl' => $effectiveUrl];
}


// coördinaten uit een Google Maps url/pagina halen (@lat,lon en varianten)
function haalGoogleCoords($opgehaald) {
    $tekst = $opgehaald['effectiveUrl'] . ' ' . $opgehaald['body'];

    $patronen = [
        '/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/',
        '/@(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/',
        '/[?&]q=(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/',
        '/[?&]ll=(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/',
    ];

    foreach ($patronen as $patroon) {
        if (preg_match($patroon, $tekst, $match)) {
            return ['lat' => (float) $match[1], 'lon' => (float) $match[2]];
        }
    }

    return null;
}


// coördinaten uit een Park4Night-pagina halen (lat=...&lng=... in de paginabron)
function haalPark4nightCoords($opgehaald) {
    if (preg_match('/[?&]lat=(-?\d{1,3}\.\d+)(?:&amp;|&)lng=(-?\d{1,3}\.\d+)/', $opgehaald['body'], $match)) {
        return ['lat' => (float) $match[1], 'lon' => (float) $match[2]];
    }

    return null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige JSON-gegevens.']);
        exit;
    }

    $link = trim($data['link'] ?? '');

    if ($link === '' || strlen($link) > 500 || !filter_var($link, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige link.']);
        exit;
    }

    $onderdelen = parse_url($link);

    if (!isset($onderdelen['scheme'], $onderdelen['host']) || !in_array(strtolower($onderdelen['scheme']), ['http', 'https'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige link.']);
        exit;
    }

    $type = bepaalLinkType($onderdelen['host']);

    if ($type === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Enkel Google Maps- en Park4Night-links worden ondersteund.']);
        exit;
    }

    $opgehaald = haalUrlOp($link);

    if ($opgehaald === null) {
        http_response_code(502);
        echo json_encode(['success' => false, 'message' => 'Kon de link niet ophalen.']);
        exit;
    }

    $coords = $type === 'google' ? haalGoogleCoords($opgehaald) : haalPark4nightCoords($opgehaald);

    if ($coords === null) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Geen coördinaten gevonden in deze link.']);
        exit;
    }

    if ($coords['lat'] < -90 || $coords['lat'] > 90 || $coords['lon'] < -180 || $coords['lon'] > 180) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Coördinaten buiten bereik.']);
        exit;
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'lat' => $coords['lat'], 'lon' => $coords['lon']]);

    $conn->close();
}
?>
