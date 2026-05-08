<?php
require_once 'dbconnect.php';

// Als al ingelogd, doorsturen naar checklist
if (isset($_SESSION["ingelogd"]) && $_SESSION["ingelogd"] === true) {
    header("location: checklist.php");
    exit;
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



<!--main-->

<div class="container-lg mt-2">

    <?php include 'inlogformulier.php';?>

</div>



<?php include 'footer.php'; ?>



<script>



// LOGIN FORMULIER - fetch API
document.getElementById('loginForm').addEventListener('submit', async (event) => {

    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const psw = document.getElementById('psw').value.trim();

    const usernameInput = document.getElementById('username');
    const pswInput = document.getElementById('psw');

    const usernameErr = document.getElementById('usernameErr');
    const pswErr = document.getElementById('pswErr');

    const loginError = document.getElementById('loginError');

    // Reset foutmeldingen
    usernameInput.classList.remove('is-invalid');
    pswInput.classList.remove('is-invalid');
    usernameErr.textContent = '';
    pswErr.textContent = '';
    loginError.classList.add('d-none');


    // Client-side validatie
    let valid = true;

    const usernamePattern = /^[a-zA-Z0-9_\-]{5,15}$/;

    if (!username) {
        usernameInput.classList.add('is-invalid');
        usernameErr.textContent = 'Gebruikersnaam mag niet leeg zijn.';
        valid = false;

    } else if (!usernamePattern.test(username)) {
        usernameInput.classList.add('is-invalid');
        usernameErr.textContent = '5 tot 15 tekens: letters, cijfers, _ of -';
        valid = false;
    }

    const pswPattern = /^\S{8,}$/;

    if (!psw) {
        pswInput.classList.add('is-invalid');
        pswErr.textContent = 'Wachtwoord mag niet leeg zijn.';
        valid = false;

    } else if (!pswPattern.test(psw)) {
        pswInput.classList.add('is-invalid');
        pswErr.textContent = 'Minimaal 8 tekens, geen spaties.';
        valid = false;
    }


    if (!valid) return;



    // Fetch naar API
    try {

        const response = await fetch('API/inlog_invoer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, psw })
        });

        const result = await response.json();

        if (result.success) {
            window.location.href = 'checklist.php';
        } else {
            // Fout op specifiek veld tonen
            if (result.field === 'username') {
                usernameInput.classList.add('is-invalid');
                usernameErr.textContent = result.message;
            } else if (result.field === 'psw') {
                pswInput.classList.add('is-invalid');
                pswErr.textContent = result.message;
            } else {
                loginError.textContent = result.message || 'Aanmelden mislukt.';
                loginError.classList.remove('d-none');
            }
        }

    } catch (error) {
        console.error('Fout bij aanmelden:', error);
        loginError.textContent = 'Er is een fout opgetreden. Probeer opnieuw.';
        loginError.classList.remove('d-none');
    }

});

</script>



</body>
</html>
