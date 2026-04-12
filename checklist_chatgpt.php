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
 <form id="checklist-form">
    <input type="hidden" id="checklist_id" name="checklist_id" value="">


    <div class="row g-4 mt-1">


        <!-- locatie card -->
        <div class="col-md-6">
                    
            <div class="card h-100 shadow-sm">


                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Locatie
                </div>


                <div class="card-body">

                    <label class="form-label" for="reisnaam">Reisnaam</label>
                    <input class="form-control mb-3" type="text" id="reisnaam" name="reisnaam" placeholder="Bijv. Zomertrip 2026">

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

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Extra items
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input class="form-control" type="text" id="extra-item-input" placeholder="Extra item toevoegen">
                        <button class="btn btn-outline-dark" type="button" id="add-extra-item-button">Toevoegen</button>
                    </div>
                    <ul class="list-group" id="extra-items-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-alfasage text-darksage fw-bold">
                    Bestaande reizen
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="saved-checklists">Kies een reis om te laden</label>
                        <select class="form-select" id="saved-checklists"></select>
                        <button type="button" class="btn btn-outline-dark mt-3" id="load-checklist-button">Laad reis</button>
                    </div>
                    <div id="messageBox"></div>
                </div>
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
                            <input class="form-check-input me-2 item-checkbox" type="checkbox" id="foodCheckbox<?= $index ?>" name="items[]" value="<?= htmlspecialchars($item) ?>" data-category="food">
                            <label class="form-check-label" for="foodCheckbox<?= $index ?>"><?= $item ?></label>
                        </li>
                        
                    <?php endforeach; ?>
                 </ul>

                </div>


                <div class="card-footer">
                    <div class="text-muted">Vink ingrediënten aan of voeg later extra items toe.</div>
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
                            <input class="form-check-input me-2 item-checkbox" type="checkbox" id="stuffCheckbox<?= $index ?>" name="items[]" value="<?= htmlspecialchars($item) ?>" data-category="stuff">
                            <label class="form-check-label" for="stuffCheckbox<?= $index ?>"><?= $item ?></label>
                        </li>

                    <?php endforeach; ?>
                 </ul>

                </div>


                <div class="card-footer">
                    <div class="text-muted">Vink spullen aan of gebruik later de extra items-sectie.</div>
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

<script>
    const extraItems = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadChecklists();

        document.getElementById('add-extra-item-button').addEventListener('click', () => {
            const input = document.getElementById('extra-item-input');
            const value = input.value.trim();
            if (value !== '') {
                addExtraItem(value);
                input.value = '';
            }
        });

        document.getElementById('checklist-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            await submitChecklist();
        });

        document.getElementById('load-checklist-button').addEventListener('click', async () => {
            const select = document.getElementById('saved-checklists');
            const checklistId = parseInt(select.value, 10);
            if (!checklistId) {
                setMessage('warning', 'Kies eerst een opgeslagen reis.');
                return;
            }
            await loadChecklist(checklistId);
        });
    });

    function setMessage(type, message) {
        const box = document.getElementById('messageBox');
        box.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
    }

    function addExtraItem(text) {
        const cleaned = text.trim();
        if (!cleaned) {
            setMessage('warning', 'Voer een extra item in.');
            return;
        }
        if (extraItems.includes(cleaned)) {
            setMessage('warning', 'Dit item staat al in de lijst.');
            return;
        }
        extraItems.push(cleaned);
        updateExtraItemsList();
    }

    function removeExtraItem(text) {
        const index = extraItems.indexOf(text);
        if (index !== -1) {
            extraItems.splice(index, 1);
            updateExtraItemsList();
        }
    }

    function updateExtraItemsList() {
        const list = document.getElementById('extra-items-list');
        list.innerHTML = '';

        extraItems.forEach((item) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `
                <span>${item}</span>
                <button type="button" class="btn btn-sm btn-outline-danger">Verwijder</button>
            `;
            li.querySelector('button').addEventListener('click', () => removeExtraItem(item));
            list.appendChild(li);
        });
    }

    function getSelectedItems() {
        const checkedItems = Array.from(document.querySelectorAll('.item-checkbox:checked')).map((input) => input.value);
        return Array.from(new Set([...checkedItems, ...extraItems]));
    }

    async function submitChecklist() {
        const reisnaam = document.getElementById('reisnaam').value.trim();
        const land = document.getElementById('land').value.trim();
        const regio = document.getElementById('regio').value.trim();
        const jaar = document.getElementById('jaar').value.trim();
        const mnWk = document.getElementById('mnWk').value.trim();
        const checklistId = document.getElementById('checklist_id').value;
        const items = getSelectedItems();

        if (!reisnaam || !land || !regio || !jaar || !mnWk) {
            setMessage('danger', 'Vul eerst de reisgegevens in.');
            return;
        }

        const payload = {
            reisnaam,
            land,
            regio,
            jaar,
            mnWk,
            items,
        };

        if (checklistId) {
            payload.checklist_id = parseInt(checklistId, 10);
        }

        try {
            const response = await fetch('API/insert_checklist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                setMessage('danger', result.message || 'Er is iets misgegaan bij het opslaan.');
                return;
            }

            document.getElementById('checklist_id').value = result.checklist_id || '';
            setMessage('success', result.message || 'Checklist opgeslagen.');
            await loadChecklists();
        } catch (error) {
            setMessage('danger', 'Fout bij verbinding met de server.');
            console.error(error);
        }
    }

    async function loadChecklists() {
        try {
            const response = await fetch('API/get_checklists.php');
            const checklists = await response.json();
            const select = document.getElementById('saved-checklists');
            select.innerHTML = '<option value="">-- Kies een reis --</option>';

            checklists.forEach((checklist) => {
                const option = document.createElement('option');
                option.value = checklist.ChecklistID;
                option.textContent = `${checklist.ReisNaam} (${checklist.Land} ${checklist.Jaar})`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Kon checklistlijst niet laden:', error);
        }
    }

    async function loadChecklist(checklistId) {
        try {
            const response = await fetch(`API/get_checklist.php?id=${checklistId}`);
            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                setMessage('danger', result.message || 'Kon de geselecteerde reis niet laden.');
                return;
            }

            fillChecklistForm(result.checklist, result.items);
            setMessage('success', 'Checklist geladen. Je kunt deze nu bewerken.');
        } catch (error) {
            setMessage('danger', 'Fout bij het ophalen van de reis.');
            console.error(error);
        }
    }

    function fillChecklistForm(checklist, items) {
        document.getElementById('checklist_id').value = checklist.ChecklistID || '';
        document.getElementById('reisnaam').value = checklist.ReisNaam || '';
        document.getElementById('land').value = checklist.Land || '';
        document.getElementById('regio').value = checklist.Regio || '';
        document.getElementById('jaar').value = checklist.Jaar || '';
        document.getElementById('mnWk').value = checklist.Periode || '';

        const checkboxValues = new Set(Array.from(document.querySelectorAll('.item-checkbox')).map((checkbox) => checkbox.value));
        document.querySelectorAll('.item-checkbox').forEach((checkbox) => {
            checkbox.checked = checkboxValues.has(checkbox.value) && items.some((item) => item.ItemName === checkbox.value);
        });

        extraItems.length = 0;
        items.forEach((item) => {
            if (!checkboxValues.has(item.ItemName)) {
                extraItems.push(item.ItemName);
            }
        });
        updateExtraItemsList();
    }
</script>

</body>

</html>