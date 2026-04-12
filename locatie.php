<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Locaties Dashboard</title>


<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>-->

<!-- Custom CSS -->
<link href="custom.css" rel="stylesheet">


<!-- Plotly -->
<!--<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>-->

<!-- Leaflet -->
<!--<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>-->

</head>


<body>

<div class="container-fluid mt-3">

  <?php include 'header.php';?>   
  <?php include 'navbar.php';?>

</div>



<!--main-->

<div class="container-fluid mt-4">

  <div class="card shadow-sm p-3">
      <h1 class="text-darksage">Best time, best places</h1> <!-- margin top en bottom -->
  </div>
  
  <div class="charts mt-4">

    <div class="card shadow-sm p-3">
      <?php include 'grafiek_plotly.php'; ?>
    </div>

    <div class="card shadow-sm p-3">
      <?php include 'map_leaf_places.php'; ?>
    </div>

  </div>


</div>






<?php include 'footer.php';?>

</body>

</html>