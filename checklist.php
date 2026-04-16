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
$camperFood = [];

$camperStuff = [];
?>


<!--main-->

<!-- dropdown menu -->
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

                 <ul class="list-group list-group-flush" id="foodList"></ul>
                     
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

                 <ul class="list-group list-group-flush" id="stuffList"></ul>

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

const DEBUG = true; 


let checklistId = null;


// EVENTLISTENER DROPDOWN MENU (UI - SWITCHER)
document.getElementById('checklistSelect').addEventListener('change', function () {

    const selectedId = this.value; // waarde van gekozen option

    if (selectedId === "") {  // als er geen checklist gekozen is --nieuwe checklist

        checklistId = null; // er is geen bestaande checklist

        document.getElementById("create_list").style.display = "block"; // dan create_list fomulier zichtbaar in UI
        document.getElementById("update_list").style.display = "none"; // update_list onzichtbaar

        return; //stop hier
    }

    checklistId = selectedId; // als er wel een checklist gekozen is, bewaar het ID 

    // debug info in console
    if (DEBUG) {
        console.log("Geselecteerde checklist:", checklistId);
    } 

    document.getElementById("create_list").style.display = "none"; // dan create_list verbergen
    document.getElementById("update_list").style.display = "block"; // update_list tonen

    loadChecklistItems(checklistId);

});


document.addEventListener('DOMContentLoaded', loadChecklists); //voer functie loadChecklists uit wanneer de HTML pagina geladen is
document.addEventListener('DOMContentLoaded', loadItems);


// DROPDOWN <select> MENU VULLEN MET CHECKLISTS UIT DATABASE (DATA - LOADER) MET FETCH API
async function loadChecklists() {
    try {
        // checklists ophalen
        const response = await fetch('API/get_checklists.php'); // request naar backend
        const data = await response.json(); //JSON omzetten naar JS-array, data = lijst van checklists

        const select = document.getElementById('checklistSelect');

        // voorkom dubbele opties als pagina opnieuw geladen wordt
        select.innerHTML = '<option value="">-- nieuwe checklist --</option>';
        // voor elke checklist een option maken
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id; // de waarde wordt het ID
            option.textContent = item.titel + " (" + item.jaar + ")";
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Fout bij laden checklists:", error);
    }
}


// ITEM - LOADER
async function loadItems() {
    try {
        const response = await fetch('API/get_items.php');
        const data = await response.json();

        const foodList = document.getElementById('foodList');
        const stuffList = document.getElementById('stuffList');

        foodList.innerHTML = '';
        stuffList.innerHTML = '';

        data.forEach(item => {

            const li = document.createElement('li');
            li.className = 'list-group-item';

            li.innerHTML = `
                <input class="form-check-input me-2"
                    type="checkbox"
                    name="${item.categorie}[]"
                    value="${item.id}"
                    id="${item.categorie}_${item.id}">

                <label for="${item.categorie}_${item.id}">
                    ${item.naam}
                </label>
            `;

            if (item.categorie === 'food') {
                foodList.appendChild(li);
            } else {
                stuffList.appendChild(li);
            }
        });

    } catch (error) {
        console.error("Fout bij laden items:", error);
    }
}


// bij selectie van checklist → items ophalen uit tbl_checklist_items
async function loadChecklistItems(id) {
    try {
        const response = await fetch('API/get_checklist_items.php?checklist_id=' + id);
        const data = await response.json();

        // eerst alles unchecken
        document.querySelectorAll('#foodList input, #stuffList input')
            .forEach(cb => cb.checked = false);

        data.forEach(item => {

            const checkboxId = item.categorie + '_' + item.item_id;
            const checkbox = document.getElementById(checkboxId);

            if (checkbox) {
                checkbox.checked = item.checked == 1;
            }
        });

    } catch (error) {
        console.error("Fout bij laden checklist items:", error);
    }
}


// NIEUWE LIJST MAKEN IN FORMULIER CREATE_LIST OF BESTAANDE LIJST UPDATEN MET FETCH API
// Functie voor wanneer je klikt op 'Opslaan' in eerste formulier
document.getElementById('create_list').addEventListener('submit', async (event) => {
    
    event.preventDefault(); // voorkom standaard formulierverzending

    // data ophalen
    const form = event.target; // event.target bevat het HTML element dat het evenement veroorzaakt heeft ( = het formulier) 
    const formData = new FormData(form); // leest alle inputvelden

    // formData converteren naar JSON
    const data = {};
    formData.forEach((value, key) => { // formData omzetten in Javascript-object
        data[key] = value; // bewaren in data-object
    });
    
    // bepalen create of update
    try {
        // endpoint nieuwe checklist maken
        let url = 'API/create_checklist.php';

        // indien checklistID bestaat (en dus lijst gecreëerd is), update_checklist
        if (checklistId !== null) {
            data.id = checklistId; // voeg ID toe
            url = 'API/update_checklist.php'; // gebruik update API
        }

        // Fetch API-aanroep stuurt JSON naar PHP API (data versturen naar backend)
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
   
        const result = await response.json();

        if (response.ok) {

            // nieuwe checklist - ID opslaan
            if (checklistId === null) {
                checklistId = result.id;
            }

            console.log("Checklist ID:", checklistId);

            // hidden input maken of updaten
            let existingInput = document.querySelector('input[name="checklist_id"]'); // kijk of input bestaat

            if (!existingInput) { // als geen input bestaat
                let input = document.createElement("input"); // maak input
                input.type = "hidden";
                input.name = "checklist_id";
                input.value = checklistId;
                document.getElementById("update_list").appendChild(input);

            } else { // als wel input bestaat
                existingInput.value = checklistId; // update waarde
            }

            // UI na opslaan: toon update formulier
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