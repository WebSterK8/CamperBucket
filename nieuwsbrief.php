<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$msg_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Autoloader van Composer inschakelen
  require 'vendor/autoload.php';
  

  // Laad configuratie
  $config = require 'config.php';

  $email = $_POST['email'];
  $name = $_POST['naam'];
  $mail = new PHPMailer(true);

  try {

    // Serverinstellingen
    $mail->isSMTP();
    $mail->Host = $config['smtp']['host']; // SMTP-server 
    $mail->SMTPAuth = $config['smtp']['auth'];
    $mail->Username = $config['smtp']['username']; // Je SMTP-gebruikersnaam
    $mail->Password = $config['smtp']['password']; // Je (SMTP-wachtwoord of) appwachtwoord
    $mail->SMTPSecure = $config['smtp']['encryption']; // Beveiligingstype
    $mail->Port = $config['smtp']['port']; // Poortnummer

    // Afzender
    $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);

    // Ontvanger
    $mail->addAddress($email, $name); 

    // Inhoud van de e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Bevestiging inschrijving nieuwsbrief';
    // HTML-versie van de mail
    $mail->Body = '<p>Beste ' . htmlspecialchars($name) . ',</p>
    <p>Bedankt voor je inschrijving voor onze nieuwsbrief!</p>
    <p>We kijken ernaar uit je op de hoogte te houden van onze updates.</p>';
    // Alleen-tekst versie van de mail
    $mail->AltBody = 'Beste ' . $name . ',\nBedankt voor je inschrijving voor onze nieuwsbrief! 
    We kijken ernaar uit je op de hoogte te houden van onze updates.';

    // Verzend de e-mail
    $mail->send();
    $message = 'E-mail succesvol verzonden!';
    $msg_type = 'success';

  } catch (Exception $e) {
    $message = "E-mail kon niet worden verzonden. Fout: {$mail->ErrorInfo}";
    $msg_type = 'danger';
  } 

}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nieuwsbrief</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="custom.css" rel="stylesheet">


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
  
input {   
    background-color: var(--beige) !important;
    color: var(--darksage) !important;
    border: 2px solid;
    border-radius: 5px;
}

input:focus {
    outline: 2px solid var(--darksage) !important; 
    box-shadow: 0 0 10px 3px rgba(72, 97, 74, 0.6) !important;
    transition: box-shadow 0.3s, outline 0.3s;
}

.custom-alert {
  color: var(--darksage) !important; 
  font-weight: 500; 
}


</style>

</head>

<body>


<div class="container-fluid mt-3">

    <?php include 'header.php';?>   
    <?php include 'navbar.php';?>


</div>



<!--main-->

<div class="container-lg mt-2">

  <div class="mt-2 p-5 bg-alfasage text-darksage text-center rounded"> 

   <h1>Nieuwsbrief</h1>

   <p>Wenst u onze nieuwsbrief te ontvangen?</p>

   <!-- Meldingen -->
    <?php if(!empty($message)): ?>
    <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show custom-alert" role="alert">
      <?php echo $message; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
    </div>
    <?php endif; ?>


    <!-- Formulier -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

     <p>Naam: <input type="text" id="naam" name="naam"></p>

     <p>E-mail: <input type="email" id="email" name="email" required></p>
     

     <button type="submit" name="submit" class="btn btn-outline-dark">Inschrijven</button>

    </form>

  </div>

</div>



<?php include 'footer.php';?>



</body>

</html>