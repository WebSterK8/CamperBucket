<?php
require_once 'dbconnect.php';

// Als al ingelogd, doorsturen naar checklist
if (isset($_SESSION["ingelogd"]) && $_SESSION["ingelogd"] === true) {
    header("location: checklist.php");
    exit;
}

$username = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Gebruikersnaam mag niet leeg zijn.";
    } else {
        $username = trim(htmlspecialchars($_POST["username"], ENT_QUOTES, 'UTF-8'));
    }

    if (empty(trim($_POST["psw"]))) {
        $password_err = "Wachtwoord mag niet leeg zijn.";
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, gebruikersnaam, wachtwoord, rol FROM tbl_gebruikers WHERE gebruikersnaam = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $gebruikersnaam, $wachtwoord, $rol);
                    if ($stmt->fetch()) {
                        if (password_verify($_POST["psw"], $wachtwoord)) {
                            $_SESSION["ingelogd"] = true;
                            $_SESSION["ID"] = $id;
                            $_SESSION["Gebruikersnaam"] = $gebruikersnaam;
                            $_SESSION["Rol"] = $rol;
                            header("location: checklist.php");
                            exit;
                        } else {
                            $login_err = "Foute gebruikersnaam of wachtwoord.";
                        }
                    }
                } else {
                    $login_err = "Foute gebruikersnaam of wachtwoord.";
                }
            } else {
                $login_err = "Er is iets verkeerd gelopen. Probeer opnieuw.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CamperBucket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container-fluid mt-3">
    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>
</div>

<div class="container-lg mt-4">

    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">

            <div class="card shadow-sm">

                <div class="card-header bg-alfasage text-darksage fw-bold text-center">
                    Aanmelden
                </div>

                <div class="card-body">

                    <?php if (!empty($login_err)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?php echo $login_err; ?>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off" novalidate>

                        <div class="mb-3">
                            <label for="username" class="form-label">Gebruikersnaam</label>
                            <input type="text"
                                   class="form-control <?php echo !empty($username_err) ? 'is-invalid' : ''; ?>"
                                   id="username" name="username"
                                   value="<?php echo $username; ?>"
                                   maxlength="50" autofocus>
                            <?php if (!empty($username_err)): ?>
                                <div class="invalid-feedback"><?php echo $username_err; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="psw" class="form-label">Wachtwoord</label>
                            <input type="password"
                                   class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>"
                                   id="psw" name="psw">
                            <?php if (!empty($password_err)): ?>
                                <div class="invalid-feedback"><?php echo $password_err; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-dark">Inloggen</button>
                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

<?php include 'footer.php'; ?>

</body>
</html>
