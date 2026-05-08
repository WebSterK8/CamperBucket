<?php
require_once 'dbconnect.php';
require_once 'controlelogin.php';
?>

<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Afbeeldingen</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="custom.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>


<body>


<div class="container-fluid mt-3">

    <?php include 'header.php';?>   
    <?php include 'navbar.php';?>


</div>



<!--main-->

<div class="container-lg mt-2">

<?php include 'bootstrapLightGallery.php'?>

</div>



<div class="container-lg mt-2">

    <div class="row gx-3 gy-2">

        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 1</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 2</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 3</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 4</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 5</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-lg-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Afbeelding 6</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        

    </div>

</div>



<?php include 'footer.php';?>

</body>

</html>