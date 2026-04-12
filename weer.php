<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Weer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link href="custom.css" rel="stylesheet">

</head>


<body>

<div class="container-fluid mt-3">

    <?php include 'header.php';?>   
    <?php include 'navbar.php';?>


</div>




<!--main-->


<div class="container-lg mt-2">

    <div class="mt-2 p-5 bg-secondary text-light text-center rounded">
        <h1>Map</h1>
    </div>

</div>




<div class="container-lg mt-2">

    <div class="row gx-3 gy-2">

        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Column 1</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Column 2</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Column 3</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>

    </div>
    
</div>

<?php include 'footer.php';?>

</body>


</html>