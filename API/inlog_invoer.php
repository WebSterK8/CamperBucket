<?php
require_once '../dbconnect.php'; // veilige database connectie (start ook sessie)

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ongeldige gegevens.']);
        exit;
    }

    // Input opschonen
    $username = trim($data['username'] ?? '');
    $password = trim($data['psw'] ?? '');

    // Input validatie: verplichte velden
    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'field' => 'username', 'message' => 'Gebruikersnaam mag niet leeg zijn.']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]{5,15}$/', $username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'field' => 'username', 'message' => '5 tot 15 tekens: letters, cijfers, _ of -']);
        exit;
    }

    if (empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'field' => 'psw', 'message' => 'Wachtwoord mag niet leeg zijn.']);
        exit;
    }

    if (!preg_match('/^\S{8,}$/', $password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'field' => 'psw', 'message' => 'Minimaal 8 tekens, geen spaties.']);
        exit;
    }

    // Input beveiliging
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    // Gebruiker opzoeken met prepared statement (SQL injection beveiliging)
    $sql = "SELECT id, gebruikersnaam, wachtwoord, rol FROM tbl_gebruikers WHERE gebruikersnaam = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Er is iets verkeerd gelopen. Probeer opnieuw.']);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['wachtwoord'])) {

            // Sessievariabelen instellen
            $_SESSION["ingelogd"] = true;
            $_SESSION["ID"] = $row['id'];
            $_SESSION["Gebruikersnaam"] = $row['gebruikersnaam'];
            $_SESSION["Rol"] = $row['rol'];

            http_response_code(200);
            echo json_encode(['success' => true]);

        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Foute gebruikersnaam of wachtwoord.']);
        }

    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Foute gebruikersnaam of wachtwoord.']);
    }

    $stmt->close();
    $conn->close();
}
?>
