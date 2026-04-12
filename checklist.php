<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CheckList</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="custom.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

</head>


<body>

<div class="container-fluid mt-3">

    <?php include 'header.php';?>   
    <?php include 'navbar.php';?>


</div>


<?php
$camperFood = [

    'Water',
    'Koffie',
    'Thee',
    'Ontbijt',

    'Noten',

    'Pasta',
    'Rijst',

    'Soep',
    'Maiswafels',
    'Hummus',
    'Avocado',

    'Diepvries spinazie',
    'Vissticks'
    
];

$camperStuff = [

    'Slaapzak',
    'Hoofdkussens',
    'Hoeslaken',

    'Handdoek',
    'Washandje',
    'Toiletgerief',

    'Sloefen',

    'Afwasmiddel',
    'Handdoek',
    'Spons',
    'Vod',

    'Borden',
    'Bestek',

    'Kookpot',
    
];
?>


<!--main-->

<div class="container-lg mt-2">

 <!-- bootstrap formulier met bootstrap componenten: card - listgroup - checkboxes - input group - addons - buttons-->
 <form action="/action_checklist.php">


    <div class="row g-4 mt-1">


        <!-- locatie card -->
        <div class="col-md-6">
                    
            <div class="card h-100 shadow-sm">


                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Locatie
                </div>


                <div class="card-body">

                    <label class="form-label" for="land">Land</label>
                    <input class="form-control" type="text" id="land">
                    
                    <label class="form-label" for="regio">Regio</label>
                    <input class="form-control" type="text" id="regio">

                </div>
                


                <!--<div class="card-footer"></div>-->


            </div>

        </div>


        <!-- periode card -->
        <div class="col-md-6">

            <div class="card h-100 shadow-sm">


                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Periode
                </div>


                <div class="card-body">

                    <label class="form-label" for="jaar">Jaar</label>
                    <input class="form-control" type="text" id="jaar">
                    
                    <label class="form-label" for="mnWk">Maand/week</label>
                    <input class="form-control" type="text" id="mnWk">

                </div>


                <!--<div class="card-footer"></div>-->

            </div>

        </div>

    </div>


   

    <div class="row g-4 mt-1">

        <!-- Camperfood card met list group en checkboxes -->
        <div class="col-md-6">

            <div class="card h-100 shadow-sm">
                
                <div class="card-header bg-alfasage text-darksage fw-bold">
                    CamperFood
                </div>
                

                <div class="card-body">

                 <ul class="list-group list-group-flush">
                    <?php foreach ($camperFood as $index => $item): ?>

                        <li class="list-group-item">
                            <input class="form-check-input me-2" type="checkbox" id="foodCheckbox<?= $index ?>">
                            <label class="form-check-label" for="foodCheckbox<?= $index ?>"><?= $item ?></label>
                        </li>
                        
                    <?php endforeach; ?>
                 </ul>

                </div>


                <div class="card-footer">

                    <!-- input group met button addon -->
                    <div class="input-group m-1">
                        <!--<label class="form-label" for="food">Extra food toevoegen aan lijst</label>-->
                        <input class="form-control"type="text" id="food" placeholder=" Extra food toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon1">
                        <button class="btn btn-outline-dark" type="button" id="button-addon1">Toevoegen</button>
                    </div>

                </div>

            </div>

        </div>


        <!-- Camperstuff card met list group en checkboxes -->
        <div class="col-md-6">

            <div class="card h-100 shadow-sm">

                <div class="card-header bg-alfasage text-darksage fw-bold">
                    CamperStuff
                </div>


                <div class="card-body">

                 <ul class="list-group list-group-flush">
                    <?php foreach ($camperStuff as $index => $item): ?>

                        <li class="list-group-item">
                            <input class="form-check-input me-2" type="checkbox" id="stuffCheckbox<?= $index ?>">
                            <label class="form-check-label" for="stuffCheckbox<?= $index ?>"><?= $item ?></label>
                        </li>

                    <?php endforeach; ?>
                 </ul>

                </div>


                <div class="card-footer">

                    <!-- input group met button addon -->
                    <div class="input-group m-1">
                        <!--<label class="form-label" for="stuff">Extra stuff</label>-->
                        <input class="form-control"type="text" id="stuff" placeholder=" Extra stuff toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon2">
                        <button class="btn btn-outline-dark" type="button" id="button-addon2">Toevoegen</button>
                    </div>

                </div>

            </div>

        </div>

    </div>



    

    <div class="row mt-4">

       <!-- Formulier opslaan card-->
        <div class="col-12">

            <div class="card h-100 shadow-sm">

                <div class="card-body">

                 <button type="submit" class="btn btn-outline-dark">Opslaan</button>

                </div>

            </div>

        </div>

    </div>
    


 </form>

</div>


<?php include 'footer.php';?>

</body>

</html>