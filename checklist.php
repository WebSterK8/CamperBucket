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
                    <input class="form-control" type="text" id="land" name="land" maxlength="50" pattern="[a-zA-ZÀ-ÿ\s\-']+" required title="Alleen letters, spaties, koppeltekens en apostrofs toegestaan">
                    
                    <label class="form-label" for="regio">Regio</label>
                    <input class="form-control" type="text" id="regio" name="regio" maxlength="100" pattern="[a-zA-ZÀ-ÿ\s\-']+" title="Alleen letters, spaties, koppeltekens en apostrofs toegestaan">

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
                    <input class="form-control" type="text" id="jaar" name="jaar" maxlength="4" pattern="[0-9]{4}" required title="Voer een geldig jaar in (4 cijfers)">
                    
                    <label class="form-label" for="mnWk">Maand/week</label>
                    <input class="form-control" type="text" id="mnWk" name="mnWk" maxlength="50" pattern="[a-zA-Z0-9\s\-\/]+" title="Alleen letters, cijfers, spaties, koppeltekens en slash toegestaan">

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
                 <button type="button" id="btn-verwijder" class="btn btn-outline-danger ms-2" style="display:none;">Verwijderen</button>

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
                        <input class="form-control" type="text" id="food" maxlength="50" placeholder=" Extra food toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon1">
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
                        <input class="form-control"type="text" id="stuff" maxlength="50" placeholder=" Extra stuff toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon2">
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


const DEBUG = false;
let checklistId = null;


document.addEventListener('DOMContentLoaded', initChecklistPage);//voer functie uit wanneer de HTML pagina geladen is



// EVENTLISTENER DROPDOWN MENU (UI - SWITCHER)
document.getElementById('checklistSelect').addEventListener('change', function () {

    const selectedId = this.value; // waarde van gekozen option

    if (selectedId === "") {  // als er geen checklist gekozen is --nieuwe checklist

        checklistId = null; // er is geen bestaande checklist

        // alle checkboxes unchecken voor nieuwe lijst
        document.querySelectorAll('#foodList input, #stuffList input').forEach(cb => cb.checked = false);

        document.getElementById("create_list").style.display = "block"; // dan create_list fomulier zichtbaar in UI
        document.getElementById("update_list").style.display = "none"; // update_list onzichtbaar
        document.getElementById("btn-verwijder").style.display = "none";

        return; //stop hier
    }

    checklistId = selectedId; // als er wel een checklist gekozen is, bewaar het ID 

    // debug info in console
    if (DEBUG) {
        console.log("Geselecteerde checklist:", checklistId);
    } 


    // velden invullen met data-attributen van de geselecteerde option
    const selectedOption = this.options[this.selectedIndex];
    document.getElementById('land').value = selectedOption.dataset.land;
    document.getElementById('regio').value = selectedOption.dataset.regio;
    document.getElementById('jaar').value = selectedOption.dataset.jaar;
    document.getElementById('mnWk').value = selectedOption.dataset.maandWeek;

    // beide formulieren tonen + verwijderknop zichtbaar
    document.getElementById("create_list").style.display = "block";
    document.getElementById("update_list").style.display = "block";
    document.getElementById("btn-verwijder").style.display = "inline-block";

    loadChecklistItems(checklistId);

});


// DROPDOWN <select> MENU VULLEN MET CHECKLISTS uit tbl_checklist MET FETCH API
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
            option.value = item.id;
            option.textContent = item.land + ' ' + item.jaar;  // Veilig: textContent
            option.dataset.land = item.land;
            option.dataset.regio = item.regio ?? '';
            option.dataset.jaar = item.jaar;
            option.dataset.maandWeek = item.maand_week ?? '';
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Fout bij laden checklists:", error);
        alert("Kon checklists niet laden. Vernieuw de pagina.");
    }
}


// FASE 1:  DOM bouwen MET FETCH API
// basic items ophalen uit tbl_items en checkboxes maken (UI bouwen voor nieuwe lijst)
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


            const checkbox = document.createElement('input');
            checkbox.className = 'form-check-input me-2';
            checkbox.type = 'checkbox';
            checkbox.name = item.categorie + '[]';
            checkbox.value = item.id;
            checkbox.id = item.categorie + '_' + item.id;

            const label = document.createElement('label');
            label.htmlFor = checkbox.id;
            label.textContent = item.naam; // veilig door textContent (ipv innerHTML)
            //label.className = 'mb-0';

            li.appendChild(checkbox);
            li.appendChild(label);



            if (item.categorie === 'food') {
                foodList.appendChild(li);
            } else {
                stuffList.appendChild(li);
            }
        });

    } catch (error) {
        console.error("Fout bij laden items:", error);
        alert("Kon items niet laden. Vernieuw de pagina.");
    }
}


// FASE 2: DOM MANIPULEREN
// (bij selectie van bestaande checklist →) gegevens ophalen uit tbl_checklist_items (checkboxes uit / aan)
async function loadChecklistItems(id) {
    try {
        
        if (DEBUG) {
            console.log("Checklist items laden voor ID:", id);
        }

        const response = await fetch('API/get_checklist_items.php?checklist_id=' + id);
        const data = await response.json();

        if (DEBUG) {
            console.log("Checklist items ontvangen:", data);
        }

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
        alert("Kon checklist items niet laden.");
    }
}


// INIT CONTROLLER FLOW
async function initChecklistPage() {
    await loadItems();
    await loadChecklists();
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

        if (DEBUG) {
            console.log("FORM DATA:", data);
            console.log("URL:", url);
        }

        // Fetch API-aanroep stuurt JSON naar PHP API (data versturen naar backend)
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, // beveiliging data-overdracht
            body: JSON.stringify(data)
        });
        
   
        const result = await response.json();

        if (DEBUG) {
            console.log("API RESPONSE:", result);
        }

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

            // dropdown label updaten of nieuwe optie toevoegen
            const select = document.getElementById('checklistSelect');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.value !== "") {
                // bestaande optie updaten
                selectedOption.textContent = data.land + ' ' + data.jaar;
                selectedOption.dataset.land = data.land;
                selectedOption.dataset.regio = data.regio || '';
                selectedOption.dataset.jaar = data.jaar;
                selectedOption.dataset.maandWeek = data.mnWk || '';
            } else {
                // nieuwe optie toevoegen en selecteren
                const newOption = document.createElement('option');
                newOption.value = checklistId;
                newOption.textContent = data.land + ' ' + data.jaar;
                newOption.dataset.land = data.land;
                newOption.dataset.regio = data.regio || '';
                newOption.dataset.jaar = data.jaar;
                newOption.dataset.maandWeek = data.mnWk || '';
                select.appendChild(newOption);
                select.value = checklistId;
            }

            // UI na opslaan: verberg locatie/periode formulier, toon items formulier
            document.getElementById("create_list").style.display = "none";
            document.getElementById("update_list").style.display = "block";

        } else {                                           
            alert(result.message || 'Onbekende fout');    
        }


    } catch (error) {
        console.error("Fout:", error);
        alert("Kon checklist niet opslaan. Probeer opnieuw.");
    }

});




// AANGEVINKTE ITEMS OPHALEN
// Functie voor wanneer je klikt op 'Opslaan' in tweede formulier
document.getElementById('update_list').addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!checklistId) {
        alert("Geen checklist geselecteerd");
        return;
    }

    // alle checkboxes ophalen
    const checkedItems = [];

    document.querySelectorAll('#foodList input, #stuffList input').forEach(cb => {
        checkedItems.push({
            item_id: cb.value,
            checked: cb.checked ? 1 : 0,
            categorie: cb.name.replace('[]', '')
        });
    });

    const payload = {
        checklist_id: checklistId,
        items: checkedItems
    };

    try {
        const response = await fetch('API/save_checklist_items.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            if (DEBUG) console.log("Opslaan resultaat:", result);
        } else {
            alert("Kon checklist niet opslaan: " + (result.error || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan checklist items:", error);
        alert("Kon items niet opslaan. Probeer opnieuw.");
    }
});


// FOOD ITEM TOEVOEGEN
document.getElementById('button-addon1').addEventListener('click', async () => {

    const input = document.getElementById('food');
    const naam = input.value.trim(); // trim() validatie

    // Regex: alleen letters, spaties, koppeltekens en apostrofs toestaan
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']+$/;

    if (!naam) { // lege input check
        alert("Voer een item in");
        return;
    } 

    if (!nameRegex.test(naam)) {
        alert("Alleen letters, spaties, koppeltekens en apostrofs zijn toegestaan");
        return;
    } 


    const response = await fetch('API/add_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            naam: naam,
            categorie: 'food'
        })
    });

    const result = await response.json();

    if (result.success) {

        const foodList = document.getElementById('foodList');

        const li = document.createElement('li');
        li.className = 'list-group-item';

        const id = result.id;
        const checkboxId = 'food_' + id;

        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input me-2';
        checkbox.type = 'checkbox';
        checkbox.name = 'food[]';
        checkbox.value = id;
        checkbox.id = checkboxId;

        const label = document.createElement('label');
        label.htmlFor = checkboxId;
        label.textContent = result.naam;  // Veilig: textContent

        li.appendChild(checkbox);
        li.appendChild(label);
        foodList.appendChild(li);

        input.value = '';
    }
});





// STUFF TOEVOEGEN
document.getElementById('button-addon2').addEventListener('click', async () => {

    const input = document.getElementById('stuff');
    const naam = input.value.trim(); // trim() validatie

    // Regex: alleen letters, spaties, koppeltekens en apostrofs toestaan
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']+$/;

    if (!naam) { // lege input check
        alert("Voer een item in");
        return;
    } 

    if (!nameRegex.test(naam)) {
        alert("Alleen letters, spaties, koppeltekens en apostrofs zijn toegestaan");
        return;
    } 


    const response = await fetch('API/add_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            naam: naam,
            categorie: 'stuff'
        })
    });

    const result = await response.json();

    if (result.success) {

        const stuffList = document.getElementById('stuffList');

        const li = document.createElement('li');
        li.className = 'list-group-item';

        const id = result.id;
        const checkboxId = 'stuff_' + id;

        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input me-2';
        checkbox.type = 'checkbox';
        checkbox.name = 'stuff[]';
        checkbox.value = id;
        checkbox.id = checkboxId;

        const label = document.createElement('label');
        label.htmlFor = checkboxId;
        label.textContent = result.naam;  // Veilig: textContent

        li.appendChild(checkbox);
        li.appendChild(label);
        stuffList.appendChild(li);

        input.value = '';
    } else {
        alert("Kon item niet toevoegen: " + (result.error || "Onbekende fout"));
    }
});





// CHECKLIST VERWIJDEREN
document.getElementById('btn-verwijder').addEventListener('click', async () => {

    if (!checklistId) return;

    if (!confirm('Ben je zeker dat je deze checklist wil verwijderen?')) return;

    try {
        const response = await fetch('API/delete_checklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: checklistId })
        });

        const result = await response.json();

        if (response.ok) {

            // optie uit dropdown verwijderen
            const select = document.getElementById('checklistSelect');
            const selectedOption = select.options[select.selectedIndex];
            select.removeChild(selectedOption);
            select.value = "";

            // UI resetten
            checklistId = null;
            document.getElementById('land').value = '';
            document.getElementById('regio').value = '';
            document.getElementById('jaar').value = '';
            document.getElementById('mnWk').value = '';
            document.getElementById("update_list").style.display = "none";
            document.getElementById("btn-verwijder").style.display = "none";

            if (DEBUG) console.log("Verwijderd:", result);

        } else {
            alert(result.message || 'Fout bij verwijderen.');
        }

    } catch (error) {
        console.error("Fout bij verwijderen:", error);
        alert("Kon checklist niet verwijderen. Probeer opnieuw.");
    }
});


</script>

<?php include 'footer.php';?>

</body>

</html>