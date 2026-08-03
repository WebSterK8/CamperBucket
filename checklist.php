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

<style>
.kaart-chevron {
    font-size: 0.7rem;
    transition: transform 0.25s ease;
}

[data-bs-toggle="collapse"][aria-expanded="true"] .kaart-chevron {
    transform: rotate(180deg);
}
</style>

<?php include 'pwa_head.php'; ?>
</head>


<body>

<div class="container-fluid mt-3">

    <?php include 'header.php';?>
    <?php include 'navbar.php';?>


</div>


<!--main-->
<div id="content"> <!-- content om eventueel als PDF te exporteren-->


<div class="container-lg mt-2">

 <form id="update_list">

    <div class="row g-4 mt-1">

        <?php
        // categorieën van de checklist: slug => label
        $categorieen = [
            'persoonlijke_verzorging'    => 'Persoonlijke verzorging',
            'kledij'                     => 'Kledij',
            'slaapgerief'                => 'Slaapgerief',
            'kampeergerief'              => 'Kampeergerief',
            'keuken_huishouden'          => 'Keuken &amp; huishouden',
            'eten_drinken'               => 'Eten en drinken',
            'elektronica_administratie'  => 'Elektronica &amp; administratie',
        ];
        ?>

        <?php foreach ($categorieen as $slug => $label): ?>

        <!-- <?php echo $label; ?> card met list group en checkboxes -->
        <div class="col-md-6">

            <div class="card h-100 shadow-sm">

                <div class="card-header bg-alfasage text-darksage fw-bold d-flex justify-content-between align-items-center" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $slug; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $slug; ?>">
                    <span><?php echo $label; ?></span>
                    <span class="kaart-chevron">▼</span>
                </div>

                <div class="collapse" id="collapse-<?php echo $slug; ?>">

                    <div class="card-body">

                     <ul class="list-group list-group-flush" id="list_<?php echo $slug; ?>"></ul>

                    </div>

                    <div class="card-footer">

                        <!-- input group met button addon -->
                        <div class="input-group m-1">
                            <input class="form-control" type="text" id="item_<?php echo $slug; ?>" maxlength="50" placeholder=" Extra item toevoegen aan lijst" aria-label=" toevoegen aan lijst" aria-describedby="button-addon-<?php echo $slug; ?>">
                            <button class="btn btn-outline-dark" type="button" id="button-addon-<?php echo $slug; ?>" data-categorie="<?php echo $slug; ?>">Toevoegen</button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

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

// kleuren voor toewijzing, overeenkomstig de kleurcode uit het vroegere Google Drive-document
const TOEGEWEZEN_KLEUREN = { kaatje: 'var(--sagegreen)', ben: 'var(--blue)' };


document.addEventListener('DOMContentLoaded', initChecklistPage);//voer functie uit wanneer de HTML pagina geladen is


// bouwt één <li> op: checkbox (aan/uit vinken) + label + toewijzing + optioneel-toggle + verwijderknop
function buildItemLi(item) {

    const li = document.createElement('li');
    li.className = 'list-group-item d-flex align-items-center flex-wrap gap-2';
    li.dataset.itemId = item.id;

    const checkbox = document.createElement('input');
    checkbox.className = 'form-check-input';
    checkbox.type = 'checkbox';
    checkbox.name = item.categorie + '[]';
    checkbox.value = item.id;
    checkbox.id = item.categorie + '_' + item.id;
    checkbox.checked = item.checked == 1;

    const label = document.createElement('label');
    label.htmlFor = checkbox.id;
    label.className = 'flex-grow-1 mb-0 item-label';
    label.textContent = item.naam; // veilig door textContent (ipv innerHTML)

    // toewijzing: Kaatje / Ben / niemand
    const select = document.createElement('select');
    select.className = 'form-select form-select-sm w-auto toegewezen-select';
    select.setAttribute('aria-label', 'Toegewezen aan');

    [['', '–'], ['kaatje', 'Kaatje'], ['ben', 'Ben']].forEach(([waarde, tekst]) => {
        const option = document.createElement('option');
        option.value = waarde;
        option.textContent = tekst;
        select.appendChild(option);
    });

    select.value = item.toegewezen || '';
    select.addEventListener('change', () => updateToegewezenStyle(select));

    // optioneel: item is een 'misschien meenemen'
    const optioneelBtn = document.createElement('button');
    optioneelBtn.type = 'button';
    optioneelBtn.className = 'btn btn-sm btn-outline-secondary optioneel-toggle';
    optioneelBtn.title = 'Optioneel item (misschien meenemen)';
    optioneelBtn.textContent = '?';
    optioneelBtn.addEventListener('click', () => {
        setOptioneel(optioneelBtn, label, optioneelBtn.getAttribute('aria-pressed') !== 'true');
    });

    // item volledig verwijderen
    const deleteBtn = document.createElement('button');
    deleteBtn.type = 'button';
    deleteBtn.className = 'btn btn-sm btn-outline-danger delete-item';
    deleteBtn.title = 'Item verwijderen';
    deleteBtn.textContent = '×';
    deleteBtn.addEventListener('click', () => deleteItem(item.id, li));

    li.appendChild(checkbox);
    li.appendChild(label);
    li.appendChild(select);
    li.appendChild(optioneelBtn);
    li.appendChild(deleteBtn);

    // initiële stijl toepassen (toewijzing + optioneel)
    updateToegewezenStyle(select);
    setOptioneel(optioneelBtn, label, item.optioneel == 1);

    return li;
}


function updateToegewezenStyle(select) {
    select.style.backgroundColor = TOEGEWEZEN_KLEUREN[select.value] || '';
    select.style.color = select.value ? '#fff' : '';
}


function setOptioneel(button, label, actief) {
    button.setAttribute('aria-pressed', actief ? 'true' : 'false');
    button.classList.toggle('btn-secondary', actief);
    button.classList.toggle('btn-outline-secondary', !actief);
    label.classList.toggle('fst-italic', actief);
    label.classList.toggle('text-muted', actief);
}


// item definitief verwijderen (uit tbl_items)
async function deleteItem(id, li) {
    if (!confirm('Dit item definitief verwijderen?')) {
        return;
    }

    try {
        const response = await fetch('API/delete_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            li.remove();
        } else {
            alert("Kon item niet verwijderen: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij verwijderen item:", error);
        alert("Kon item niet verwijderen. Probeer opnieuw.");
    }
}


// DOM bouwen MET FETCH API
// items ophalen uit tbl_items (checkbox, toewijzing en optioneel zitten al op het item zelf)
async function loadItems() {
    try {
        const response = await fetch('API/get_items.php');

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

        const data = await response.json();

        document.querySelectorAll('ul[id^="list_"]').forEach(ul => ul.innerHTML = '');

        data.forEach(item => {

            const list = document.getElementById('list_' + item.categorie);

            // onbekende/verouderde categorie: overslaan
            if (!list) return;

            list.appendChild(buildItemLi(item));
        });

    } catch (error) {
        console.error("Fout bij laden items:", error);
        alert("Kon items niet laden. Vernieuw de pagina.");
    }
}


// INIT CONTROLLER FLOW
async function initChecklistPage() {
    await loadItems();
    initPdfDownload('downloadPDF', 'content', 'Checklist.pdf');
}


// ALLE ITEMS OPSLAAN
// Functie voor wanneer je klikt op 'Opslaan'
document.getElementById('update_list').addEventListener('submit', async (event) => {
    event.preventDefault();

    // alle items ophalen: checked, toewijzing en optioneel
    const items = [];

    document.querySelectorAll('ul[id^="list_"] li[data-item-id]').forEach(li => {

        const checkbox = li.querySelector('input[type="checkbox"]');
        const select = li.querySelector('.toegewezen-select');
        const optioneelBtn = li.querySelector('.optioneel-toggle');

        items.push({
            id: li.dataset.itemId,
            checked: checkbox.checked ? 1 : 0,
            toegewezen: select.value || null,
            optioneel: optioneelBtn.getAttribute('aria-pressed') === 'true' ? 1 : 0
        });
    });

    try {
        const response = await fetch('API/save_items.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items })
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


// ITEM TOEVOEGEN (herbruikbaar voor elke categorie-kaart)
function initAddItemHandler(button) {

    const categorie = button.dataset.categorie;
    const input = document.getElementById('item_' + categorie);

    button.addEventListener('click', async () => {

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

        try {
            const response = await fetch('API/add_item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ naam: naam, categorie: categorie })
            });

            // tweede verdedigingslinie: sessie verlopen
            if (checkSession(response)) return;

            const result = await response.json();

            if (result.success) {

                const list = document.getElementById('list_' + categorie);

                list.appendChild(buildItemLi({
                    id: result.id,
                    naam: result.naam,
                    categorie: result.categorie,
                    checked: 0,
                    toegewezen: null,
                    optioneel: 0
                }));

                input.value = '';
            } else {
                alert("Kon item niet toevoegen: " + (result.message || "Onbekende fout"));
            }

        } catch (error) {
            console.error("Fout bij toevoegen item:", error);
            alert("Kon item niet toevoegen. Probeer opnieuw.");
        }
    });
}

document.querySelectorAll('[id^="button-addon-"]').forEach(initAddItemHandler);


</script>

<?php include 'footer_export_PDF.php';?>

</body>

</html>
