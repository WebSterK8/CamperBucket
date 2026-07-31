<?php
require_once 'dbconnect.php';
require_once 'controlelogin.php';
?>
<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CheckList</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="camperbucket.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

<script src="functies.js"></script>

<?php include 'pwa_head.php'; ?>
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
<div id="content"> <!-- content om eventueel als PDF te exporteren-->
    

<div class="container-lg mt-2">

 <form id="update_list">

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

</div>

<!-- code client side -->


<script>


const DEBUG = false;
let checklistId = null;


document.addEventListener('DOMContentLoaded', initChecklistPage);//voer functie uit wanneer de HTML pagina geladen is


// FASE 1:  DOM bouwen MET FETCH API
// basic items ophalen uit tbl_items en checkboxes maken (UI bouwen voor nieuwe lijst)
async function loadItems() {
    try {
        const response = await fetch('API/get_items.php');

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

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

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

        const data = await response.json();

        if (DEBUG) {
            console.log("Checklist items ontvangen:", data);
        }

        // verwijder custom items van vorige checklist
        document.querySelectorAll('#foodList li[data-custom], #stuffList li[data-custom]')
            .forEach(li => li.remove());

        // eerst alles unchecken
        document.querySelectorAll('#foodList input, #stuffList input')
            .forEach(cb => cb.checked = false);

        data.forEach(item => {

            const checkboxId = item.categorie + '_' + item.item_id;
            let checkbox = document.getElementById(checkboxId);

            // custom item nog niet in DOM: <li> toevoegen
            if (!checkbox) {
                const list = item.categorie === 'food'
                    ? document.getElementById('foodList')
                    : document.getElementById('stuffList');

                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.dataset.custom = 'true';

                checkbox = document.createElement('input');
                checkbox.className = 'form-check-input me-2';
                checkbox.type = 'checkbox';
                checkbox.name = item.categorie + '[]';
                checkbox.value = item.item_id;
                checkbox.id = checkboxId;

                const label = document.createElement('label');
                label.htmlFor = checkboxId;
                label.textContent = item.naam;

                li.appendChild(checkbox);
                li.appendChild(label);
                list.appendChild(li);
            }

            checkbox.checked = item.checked == 1;
        });

    } catch (error) {
        console.error("Fout bij laden checklist items:", error);
        alert("Kon checklist items niet laden.");
    }
}


// ACTIVE CHECKLIST OPHALEN OF AANMAKEN
async function ensureChecklistId() {
    if (checklistId) {
        return checklistId;
    }

    try {
        const response = await fetch('API/get_checklists.php');

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return null;

        const data = await response.json();

        if (Array.isArray(data) && data.length > 0) {
            checklistId = data[0].id;
            return checklistId;
        }

        const createResponse = await fetch('API/create_checklist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                land: 'Nederland',
                regio: '',
                jaar: new Date().getFullYear().toString(),
                mnWk: ''
            })
        });

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(createResponse)) return null;

        const result = await createResponse.json();

        if (createResponse.ok && result.id) {
            checklistId = result.id;
            return checklistId;
        }

        throw new Error(result.message || 'Kon geen checklist aanmaken');

    } catch (error) {
        console.error('Fout bij ophalen van checklist:', error);
        alert('Kon geen actieve checklist vinden. Probeer opnieuw.');
        return null;
    }
}

// INIT CONTROLLER FLOW
async function initChecklistPage() {
    await loadItems();
    const activeChecklistId = await ensureChecklistId();
    if (activeChecklistId) {
        await loadChecklistItems(activeChecklistId);
    }
    initPdfDownload('downloadPDF', 'content', 'Checklist.pdf');
}










// AANGEVINKTE ITEMS OPHALEN
// Functie voor wanneer je klikt op 'Opslaan'
document.getElementById('update_list').addEventListener('submit', async (event) => {
    event.preventDefault();

    const activeChecklistId = await ensureChecklistId();
    if (!activeChecklistId) {
        return;
    }

    checklistId = activeChecklistId;

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

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

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
            categorie: 'food',
            checklist_id: checklistId
        })
    });

    // tweede verdedigingslinie: sessie verlopen
    if (checkSession(response)) return;

    const result = await response.json();

    if (result.success) {

        const foodList = document.getElementById('foodList');

        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.dataset.custom = 'true';

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
    } else {
        alert("Kon item niet toevoegen: " + (result.message || "Onbekende fout"));
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
            categorie: 'stuff',
            checklist_id: checklistId
        })
    });

    // tweede verdedigingslinie: sessie verlopen
    if (checkSession(response)) return;

    const result = await response.json();

    if (result.success) {

        const stuffList = document.getElementById('stuffList');

        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.dataset.custom = 'true';

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
        alert("Kon item niet toevoegen: " + (result.message || "Onbekende fout"));
    }
});


</script>

<?php include 'footer_export_PDF.php';?>

</body>

</html>