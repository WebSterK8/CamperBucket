<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)
require_once 'controlelogin.php'; // login controle

header('Content-Type: application/json');

// TIMELINE DATA OPHALEN en omzetten naar KnightLab JSON-formaat

// hardcoded query SELECT - geen gebruikersinput
$sql = "SELECT intro, land, beschrijving, foto, foto_alt,
               start_jaar, start_maand, start_dag,
               eind_jaar, eind_maand, eind_dag
        FROM tbl_reizen
        ORDER BY start_jaar ASC, start_maand ASC, start_dag ASC, id ASC";

$result = $conn->query($sql);

if (!$result) { // checken of query gelukt is voor het resultaat gebruikt wordt
    http_response_code(500);
    echo json_encode(['message' => 'Database fout']); // Veilige JSON output
    exit;
}

$timelineJson = [
    'title'  => null,
    'events' => []
];

while ($row = $result->fetch_assoc()) {

    if ($row['intro'] == 1) {

        // intro-slide: title-object voor KnightLab
        $title = [
            'text' => [
                'headline' => $row['land'] ?? '',
                'text'     => $row['beschrijving'] ?? ''
            ]
        ];

        if (!empty($row['foto'])) {
            $title['media'] = [
                'url' => $row['foto'],
                'alt' => $row['foto_alt'] ?? ''
            ];
        }

        $timelineJson['title'] = $title;

    } else {

        // gewone reis: event-object voor KnightLab
        $event = [];

        if (!empty($row['start_jaar'])) {
            $startDate = ['year' => (string) $row['start_jaar']];
            if (!empty($row['start_maand'])) $startDate['month'] = (string) $row['start_maand'];
            if (!empty($row['start_dag']))   $startDate['day']   = (string) $row['start_dag'];
            $event['start_date'] = $startDate;
        }

        if (!empty($row['eind_jaar'])) {
            $endDate = ['year' => (string) $row['eind_jaar']];
            if (!empty($row['eind_maand'])) $endDate['month'] = (string) $row['eind_maand'];
            if (!empty($row['eind_dag']))   $endDate['day']   = (string) $row['eind_dag'];
            $event['end_date'] = $endDate;
        }

        $event['text'] = [
            'headline' => $row['land'] ?? '',
            'text'     => $row['beschrijving'] ?? ''
        ];

        if (!empty($row['foto'])) {
            $event['media'] = [
                'url' => $row['foto'],
                'alt' => $row['foto_alt'] ?? ''
            ];
        }

        $timelineJson['events'][] = $event;
    }
}

echo json_encode($timelineJson); // Veilige JSON output

$conn->close();
?>
