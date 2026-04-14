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
    'Keukenhanddoek',
    'Spons',
    'Vod',

    'Borden',
    'Bestek',

    'Kookpot',
    
];
?>


<!--main-->

<div class="container-lg mt-2">

 <div class="card mt-3">

    <div class="card-body">

        <label for="checklistSelect" class="form-label">
            Kies bestaande checklist
        </label>

        <select id="checklistSelect" class="form-select">
            <option value="">-- nieuwe checklist --</option>
        </select>

    </div>

 </div>


 <!-- bootstrap formulier met bootstrap componenten: card - listgroup - checkboxes - input group - addons - buttons-->
 <form id="create_list">


    <div class="row g-4 mt-1">


        <!-- locatie card -->
        <div class="col-md-6">
                    
            <div class="card h-100 shadow-sm">


                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Locatie
                </div>


                <div class="card-body">

                    <label class="form-label" for="land">Land</label>
                    <input class="form-control" type="text" id="land" name="land">
                    
                    <label class="form-label" for="regio">Regio</label>
                    <input class="form-control" type="text" id="regio" name="regio">

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
                    <input class="form-control" type="text" id="jaar" name="jaar">
                    
                    <label class="form-label" for="mnWk">Maand/week</label>
                    <input class="form-control" type="text" id="mnWk" name="mnWk">

                </div>


                <!--<div class="card-footer"></div>-->

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


 <form id="update_list" style="display:none;">

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
                            <input class="form-check-input me-2" 
                            type="checkbox" 
                            name="food[]"
                            value="<?= $item ?>"
                            id="foodCheckbox<?= $index ?>">

                            <label class="form-check-label" for="foodCheckbox<?= $index ?>"><?= $item ?></label>
                        </li>
                        
                    <?php endforeach; ?>
                 </ul>

                </div>


                <div class="card-footer">

                    <!-- input group met button addon -->
                    <div class="input-group m-1">
                        <!--<label class="form-label" for="food">Extra food toevoegen aan lijst</label>-->
                        <input class="form-control" type="text" id="food" placeholder=" Extra food toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon1">
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
                            <input class="form-check-input me-2" 
                            type="checkbox" 
                            name="stuff[]"
                            value="<?=  $item ?>"
                            id="stuffCheckbox<?= $index ?>">

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

<!-- code client side -->


<script>

let checklistId = null;

// event
document.getElementById('checklistSelect').addEventListener('change', function () {

    const selectedId = this.value;

    if (selectedId === "") {
        checklistId = null;

        document.getElementById("create_list").style.display = "block";
        document.getElementById("update_list").style.display = "none";

        return;
    }

    checklistId = selectedId;

    console.log("Geselecteerde checklist:", checklistId);

    document.getElementById("create_list").style.display = "none";
    document.getElementById("update_list").style.display = "block";


});

// DROPDOWN
async function loadChecklists() {
    try {
        const response = await fetch('API/get_checklists.php');
        const data = await response.json();

        const select = document.getElementById('checklistSelect');

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.titel + " (" + item.jaar + ")";
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Fout bij laden checklists:", error);
    }
}

document.addEventListener('DOMContentLoaded', loadChecklists);


// NIEUWE LIJST MAKEN IN FORMULIER CREATE_LIST OF BESTAANDE LIJST UPDATEN MET FETCH API
document.getElementById('create_list').addEventListener('submit', async (event) => {
    
    event.preventDefault(); // Voorkom standaard formulierverzending

    const form = event.target; // event.target bevat het HTML element dat het evenement veroorzaakt heeft ( = het formulier) 
    const formData = new FormData(form);

    // formData converteren naar JSON
    const data = {};
    formData.forEach((value, key) => { // formData omzetten in Javascript-object
        data[key] = value; // bewaren in data-object
    });

    try {
        // endpoint
        let url = 'API/create_checklist.php';

        // indien checklistID bestaat (en dus lijst gecreëerd is), update_checklist
        if (checklistId !== null) {
            data.id = checklistId;
            url = 'API/update_checklist.php';
        }

        // Fetch API-aanroep
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {

            // enkel bij create krijg je nieuwe id
            if (checklistId === null) {
                checklistId = result.id;
            }

            console.log("Checklist ID:", checklistId);

            // hidden input updaten
            let existingInput = document.querySelector('input[name="checklist_id"]');

            if (!existingInput) {
                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "checklist_id";
                input.value = checklistId;
                document.getElementById("update_list").appendChild(input);
            } else {
                existingInput.value = checklistId;
            }

            // UI
            document.getElementById("update_list").style.display = "block";

        }

    } catch (error) {
        console.error("Fout:", error);
    }

});

                
</script>

<?php include 'footer.php';?>

</body>

</html>