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

</div>

</div>


<!-- Modal item bewerken / verwijderen -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Item bewerken</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="itemModalNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="itemModalNaam" maxlength="50" pattern="[a-zA-ZÀ-ÿ\s\-']+">

                <label class="form-label" for="itemModalToegewezen">Toegewezen aan</label>
                <select class="form-select mb-3" id="itemModalToegewezen">
                    <option value="">–</option>
                    <option value="kaatje">Kaatje</option>
                    <option value="ben">Ben</option>
                </select>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="itemModalOptioneel">
                    <label class="form-check-label" for="itemModalOptioneel">Optioneel item (misschien meenemen)</label>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-danger" id="btnItemVerwijder">Verwijderen</button>
            </div>

        </div>
    </div>
</div>


<!-- code client side -->


<script>


const DEBUG = false;

// kleuren voor toewijzing, overeenkomstig de kleurcode uit het vroegere Google Drive-document
const TOEGEWEZEN_KLEUREN = { kaatje: 'var(--sagegreen)', ben: 'var(--blue)' };


document.addEventListener('DOMContentLoaded', initChecklistPage);//voer functie uit wanneer de HTML pagina geladen is


// bouwt één <li> op: checkbox (aan/uit vinken) + label + "meer opties"-knopje
function buildItemLi(item) {

    const li = document.createElement('li');
    li.className = 'list-group-item d-flex align-items-center gap-2';
    li.dataset.itemId = item.id;
    li.dataset.toegewezen = item.toegewezen || '';
    li.dataset.optioneel = item.optioneel == 1 ? '1' : '0';

    const checkbox = document.createElement('input');
    checkbox.className = 'form-check-input flex-shrink-0';
    checkbox.type = 'checkbox';
    checkbox.name = item.categorie + '[]';
    checkbox.value = item.id;
    checkbox.id = item.categorie + '_' + item.id;
    checkbox.checked = item.checked == 1;
    checkbox.addEventListener('change', () => autosaveChecked(li));

    const label = document.createElement('label');
    label.htmlFor = checkbox.id;
    label.className = 'flex-grow-1 mb-0 item-label text-truncate';
    label.textContent = item.naam; // veilig door textContent (ipv innerHTML)

    // meer opties: toewijzen, optioneel, naam bewerken of verwijderen (via modal)
    const menuBtn = document.createElement('button');
    menuBtn.type = 'button';
    menuBtn.className = 'btn btn-sm btn-outline-dark item-menu flex-shrink-0';
    menuBtn.title = 'Meer opties';
    menuBtn.textContent = '⋮';
    menuBtn.addEventListener('click', () => openItemModal(li));

    li.appendChild(checkbox);
    li.appendChild(label);
    li.appendChild(menuBtn);

    // initiële stijl toepassen (toewijzing + optioneel)
    applyToegewezenStyle(label, item.toegewezen || '');
    applyOptioneelStyle(label, item.optioneel == 1);

    return li;
}


// tekst van het item gekleurd en vetgedrukt maken bij toewijzing aan Kaatje/Ben
function applyToegewezenStyle(label, waarde) {
    label.style.color = TOEGEWEZEN_KLEUREN[waarde] || '';
    label.style.fontWeight = waarde ? 'bold' : '';
}


// tekst van het item lichter en cursief maken bij optioneel
function applyOptioneelStyle(label, actief) {
    label.classList.toggle('fst-italic', actief);
    label.style.opacity = actief ? '0.3' : '';
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


// MODAL: item bewerken (naam, toewijzing, optioneel) of verwijderen
let itemModalLi = null;

function openItemModal(li) {
    itemModalLi = li;
    document.getElementById('itemModalNaam').value = li.querySelector('.item-label').textContent;
    document.getElementById('itemModalToegewezen').value = li.dataset.toegewezen || '';
    document.getElementById('itemModalOptioneel').checked = li.dataset.optioneel === '1';
    new bootstrap.Modal(document.getElementById('itemModal')).show();
}


// MODAL: AUTOSAVE (naam + toewijzing + optioneel) MET FETCH API
// naam: bij blur van het veld | toewijzing/optioneel: meteen bij wijziging
async function saveItemModal() {

    if (!itemModalLi) return;

    const naam = document.getElementById('itemModalNaam').value.trim(); // trim() validatie

    // Regex: alleen letters, spaties, koppeltekens en apostrofs toestaan
    const nameRegex = /^[a-zA-ZÀ-ÿ\s\-']+$/;

    if (!naam) { // lege input check
        alert("Voer een naam in");
        return;
    }

    if (!nameRegex.test(naam)) {
        alert("Alleen letters, spaties, koppeltekens en apostrofs zijn toegestaan");
        return;
    }

    const toegewezen = document.getElementById('itemModalToegewezen').value || null;
    const optioneel = document.getElementById('itemModalOptioneel').checked ? 1 : 0;

    try {
        const response = await fetch('API/update_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: itemModalLi.dataset.itemId,
                naam: naam,
                toegewezen: toegewezen,
                optioneel: optioneel
            })
        });

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {

            const label = itemModalLi.querySelector('.item-label');
            label.textContent = result.naam; // Veilig: textContent

            itemModalLi.dataset.toegewezen = toegewezen || '';
            itemModalLi.dataset.optioneel = optioneel;

            applyToegewezenStyle(label, toegewezen || '');
            applyOptioneelStyle(label, optioneel == 1);
        } else {
            alert("Kon item niet opslaan: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan item:", error);
        alert("Kon item niet opslaan. Probeer opnieuw.");
    }
}

document.getElementById('itemModalNaam').addEventListener('blur', saveItemModal);
document.getElementById('itemModalToegewezen').addEventListener('change', saveItemModal);
document.getElementById('itemModalOptioneel').addEventListener('change', saveItemModal);


// MODAL: verwijderen (hergebruikt deleteItem, sluit eerst de modal)
document.getElementById('btnItemVerwijder').addEventListener('click', () => {
    if (!itemModalLi) return;

    const li = itemModalLi;
    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
    deleteItem(li.dataset.itemId, li);
});


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


// AUTOSAVE: aangevinkte status van één item meteen opslaan bij het aan/uit vinken
async function autosaveChecked(li) {

    const checkbox = li.querySelector('input[type="checkbox"]');

    const payload = {
        items: [{
            id: li.dataset.itemId,
            checked: checkbox.checked ? 1 : 0,
            toegewezen: li.dataset.toegewezen || null,
            optioneel: li.dataset.optioneel === '1' ? 1 : 0
        }]
    };

    try {
        const response = await fetch('API/save_items.php', {
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
            alert("Kon wijziging niet opslaan: " + (result.error || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan item:", error);
        alert("Kon wijziging niet opslaan. Probeer opnieuw.");
    }
}


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
