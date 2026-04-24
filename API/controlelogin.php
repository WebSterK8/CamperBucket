<?php
// controleer of de gebruiker is ingelogd. Indien niet ingelogd >> naar login.php
if(!isset($_SESSION["ingelogd"]) || $_SESSION["ingelogd"] !== true){
    header("location: ../login.php");
    exit;
}
?>