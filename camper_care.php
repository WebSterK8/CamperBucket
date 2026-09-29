<?php
require_once 'dbconnect.php';
require_once 'controlelogin.php';
?>
<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CamperCare</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="camperbucket.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script src="functies.js"></script>

<style>
/* pijltje */
.kaart-chevron {
    font-size: 0.7rem;
    transition: transform 0.25s ease;
}

[data-bs-toggle="collapse"][aria-expanded="true"] .kaart-chevron {
    transform: rotate(180deg);
}

/* ronde vinkbolletjes, zelfde stijl als op de checklist (K = Kaatje, B = Ben) */
.cat-toggle {
    width: 1.6rem;
    height: 1.6rem;
    border-radius: 50%;
    border: 2px solid;
    background: transparent;
    padding: 0;
    font-size: 0.75rem;
    font-weight: bold;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.cat-toggle[data-persoon="kaatje"] { border-color: var(--sagegreen); color: var(--sagegreen); }
.cat-toggle[data-persoon="ben"]    { border-color: var(--blue); color: var(--blue); }
.cat-toggle[data-persoon="geen"]   { border-color: var(--darksage); color: var(--darksage); }

/* bol zonder letter iets kleiner + dunnere rand (optische compensatie, zoals op de checklist) */
.cat-toggle-klein { width: 1.45rem; height: 1.45rem; border-width: 1.5px; }

/* geen hover-effect op de bolletjes, enkel inkleuren bij .checked */
.cat-toggle:hover { background: transparent; }

/* afgevinkt: ingekleurde bol met witte letter */
.cat-toggle.checked[data-persoon="kaatje"] { background: var(--sagegreen); color: #fff; }
.cat-toggle.checked[data-persoon="ben"]    { background: var(--blue); color: #fff; }
.cat-toggle.checked[data-persoon="geen"]   { background: var(--darksage); color: #fff; }

/* notitie onder de taaknaam: klein en grijs */
.item-notitie {
    font-size: 0.8rem;
    color: var(--darksage);
    opacity: 0.8;
    white-space: pre-line; /* enters in de notitie tonen */
}

/* SLEPEN (SortableJS): geen tekstselectie of gsm-contextmenu bij lang indrukken */
ul[id^="list_"] li {
    user-select: none;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
}

/* lege lijst toch groot genoeg om er een taak in te laten vallen */
ul[id^="list_"] { min-height: 2.5rem; }

/* vastgenomen taak licht op, de plek waar ze terechtkomt is half doorzichtig */
.sortable-chosen { background-color: var(--bluegrey) !important; }
.sortable-ghost  { opacity: 0.4; }
</style>

<?php include 'pwa_head.php'; ?>
</head>


<body>

<div class="container-fluid mt-3">

    <?php include 'header.php';?>
    <?php include 'navbar.php';?>


</div>


<!--main-->
<div class="container-lg mt-5">

    <!-- titel, in dezelfde stijl als de CheckList-pagina -->
    <div class="d-flex justify-content-between align-items-center m-3 m-md-5">

        <h1 style="color: #606f60;">CamperCare</h1>

    </div>

    <div class="row g-4">

        <?php
        // categorieën van Camper Care uit de database (slug => naam), op volgorde van positie
        $categorieen = [];
        $catResult = $conn->query("SELECT slug, naam FROM tbl_care_categorie ORDER BY positie, naam");
        if ($catResult) {
            while ($catRow = $catResult->fetch_assoc()) {
                $categorieen[$catRow['slug']] = $catRow['naam'];
            }
        }
        ?>

        <?php foreach ($categorieen as $slug => $label): ?>

        <!-- <?php echo htmlspecialchars($label); ?> card met list group -->
        <div class="col-md-6">

            <div class="card h-100 shadow-sm">

                <div class="card-header bg-alfasage text-darksage fw-bold d-flex justify-content-between align-items-center">
                    <span class="d-flex align-items-center gap-2" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $slug; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $slug; ?>">
                        <span class="kaart-chevron">▼</span>
                        <span class="categorie-naam"><?php echo htmlspecialchars($label); ?></span>
                    </span>
                    <button type="button" class="btn btn-sm categorie-menu flex-shrink-0" data-slug="<?php echo $slug; ?>" title="Categorie bewerken">⋮</button>
                </div>

                <div class="collapse" id="collapse-<?php echo $slug; ?>">

                    <div class="card-body px-0">

                     <ul class="list-group list-group-flush" id="list_<?php echo $slug; ?>"></ul>

                    </div>

                    <div class="card-footer">

                        <!-- input group met button addon -->
                        <div class="input-group m-1">
                            <input class="form-control" type="text" id="item_<?php echo $slug; ?>" maxlength="100" placeholder=" Taak toevoegen" aria-label="Taak toevoegen" aria-describedby="button-addon-<?php echo $slug; ?>">
                            <button class="btn btn-outline-dark" type="button" id="button-addon-<?php echo $slug; ?>" data-categorie="<?php echo $slug; ?>">Toevoegen</button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <div class="mt-4 mb-4">
        <button type="button" class="btn btn-outline-dark" id="btnNieuweCategorie">+&nbsp;Categorie toevoegen</button>
    </div>

</div>


<!-- Modal taak bewerken / verwijderen -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Taak bewerken</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="itemModalNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="itemModalNaam" maxlength="100">

                <label class="form-label" for="itemModalNotitie">Notitie</label>
                <textarea class="form-control mb-3" id="itemModalNotitie" rows="2" maxlength="255" placeholder="Bijv. merk, maat of extra uitleg"></textarea>

                <label class="form-label" for="itemModalCategorie">Categorie</label>
                <select class="form-select mb-3" id="itemModalCategorie">
                    <?php foreach ($categorieen as $slug => $label): ?>
                    <option value="<?php echo htmlspecialchars($slug); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label" for="itemModalToegewezen">Toegewezen aan</label>
                <select class="form-select mb-3" id="itemModalToegewezen">
                    <option value="">–</option>
                    <option value="kaatje">Kaatje</option>
                    <option value="ben">Ben</option>
                    <option value="allebei">Allebei</option>
                </select>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-danger" id="btnItemVerwijder">Verwijderen</button>
            </div>

        </div>
    </div>
</div>


<!-- Modal categorie bewerken / resetten / verwijderen -->
<div class="modal fade" id="categorieModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Categorie bewerken</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="categorieModalNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="categorieModalNaam" maxlength="50">

                <!-- reset per kaart: vinkjes van enkel deze categorie uitzetten -->
                <button type="button" class="btn btn-outline-dark" id="btnCategorieReset">Alle vinkjes uit</button>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-danger" id="btnCategorieVerwijder">Verwijderen</button>
            </div>

        </div>
    </div>
</div>


<!-- Modal nieuwe categorie -->
<div class="modal fade" id="nieuweCategorieModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Nieuwe categorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="nieuweCategorieNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="nieuweCategorieNaam" maxlength="50" placeholder="Bijv. Jaarlijks onderhoud">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-dark" id="btnNieuweCategorieOpslaan">Toevoegen</button>
            </div>

        </div>
    </div>
</div>


<!-- code client side -->


<script>


// kleuren voor toewijzing, zoals op de checklist
const TOEGEWEZEN_KLEUREN = { kaatje: 'var(--sagegreen)', ben: 'var(--blue)' };

// Regex taaknaam: letters, cijfers, spaties en - ' & + / ( ) , . : ! ? (overeenkomstig backend)
const TAAK_NAAM_REGEX = /^[a-zA-ZÀ-ÿ0-9\s\-'&+\/(),.:!?]+$/;
const TAAK_NAAM_FOUT = "Alleen letters, cijfers, spaties en - ' & + / ( ) , . : ! ? zijn toegestaan";

// Regex categorie-naam: zoals de checklist, plus cijfers
const CATEGORIE_NAAM_REGEX = /^[a-zA-ZÀ-ÿ0-9\s\-'&+\/()]+$/;
const CATEGORIE_NAAM_FOUT = "Alleen letters, cijfers, spaties, koppeltekens, apostrofs, &, +, / en () zijn toegestaan";


document.addEventListener('DOMContentLoaded', initCamperCarePage);//voer functie uit wanneer de HTML pagina geladen is


// kleine helper: JSON posten naar een API-bestand, geeft het resultaat terug (of null bij verlopen sessie)
async function postJson(url, body) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });

    // tweede verdedigingslinie: sessie verlopen
    if (checkSession(response)) return null;

    return await response.json();
}


// bouwt één <li> op: vinkje(s) + naam/notitie + pijltjes + "meer opties"-knopje
function buildItemLi(item) {

    const toegewezen = item.toegewezen || '';

    const li = document.createElement('li');
    li.className = 'list-group-item d-flex align-items-center gap-2';
    li.dataset.itemId = item.id;
    li.dataset.categorie = item.categorie;
    li.dataset.toegewezen = toegewezen;
    li.dataset.notitie = item.notitie || '';
    // vinkstatussen op de <li> bewaren zodat autosave altijd de juiste waarden meestuurt
    li.dataset.checked = item.checked == 1 ? '1' : '0';
    li.dataset.checkedKaatje = item.checked_kaatje == 1 ? '1' : '0';
    li.dataset.checkedBen = item.checked_ben == 1 ? '1' : '0';

    if (toegewezen === 'allebei') {
        // twee vinkjes: ieder vinkt z'n eigen deel af (K = Kaatje, B = Ben)
        const groep = document.createElement('span');
        groep.className = 'd-flex align-items-center gap-2 flex-shrink-0';
        groep.appendChild(buildPersoonVinkje(li, item, 'kaatje'));
        groep.appendChild(buildPersoonVinkje(li, item, 'ben'));
        li.appendChild(groep);
    } else {
        // één rond bolletje; kleur volgt de toewijzing (groen/blauw), of neutraal bij geen
        li.appendChild(buildItemToggle(li, item));
    }

    // naam met daaronder (optioneel) de notitie
    const tekst = document.createElement('div');
    tekst.className = 'flex-grow-1';
    tekst.style.minWidth = '0'; // laat text-truncate werken binnen flex

    const label = document.createElement('div');
    label.className = 'item-label text-truncate';
    label.textContent = item.naam; // veilig door textContent (ipv innerHTML)

    const notitie = document.createElement('div');
    notitie.className = 'item-notitie';
    notitie.textContent = item.notitie || '';
    notitie.hidden = !item.notitie;

    tekst.appendChild(label);
    tekst.appendChild(notitie);
    li.appendChild(tekst);

    // meer opties: naam, notitie, categorie, toewijzing of verwijderen (via modal)
    const menuBtn = document.createElement('button');
    menuBtn.type = 'button';
    menuBtn.className = 'btn btn-sm categorie-menu item-menu flex-shrink-0';
    menuBtn.title = 'Meer opties';
    menuBtn.textContent = '⋮';
    menuBtn.addEventListener('click', () => openItemModal(li));
    li.appendChild(menuBtn);

    applyToegewezenStyle(label, toegewezen);

    return li;
}


// bouwt één persoonsvinkje (K of B) voor een 'allebei'-taak
function buildPersoonVinkje(li, item, persoon) {

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'cat-toggle flex-shrink-0';
    btn.dataset.persoon = persoon;
    btn.textContent = persoon === 'kaatje' ? 'K' : 'B';
    btn.title = (persoon === 'kaatje' ? 'Kaatje' : 'Ben') + ': gedaan';
    setToggleState(btn, (persoon === 'kaatje' ? item.checked_kaatje : item.checked_ben) == 1);

    btn.addEventListener('click', () => {
        const nu = !btn.classList.contains('checked');
        setToggleState(btn, nu);
        if (persoon === 'kaatje') li.dataset.checkedKaatje = nu ? '1' : '0';
        else li.dataset.checkedBen = nu ? '1' : '0';
        autosaveChecked(li);
    });

    return btn;
}


// bouwt een bolletje voor een taak; bij toewijzing Kaatje/Ben staat er een letter, anders blijft de bol leeg en kleiner
const PERSOON_LETTER = { kaatje: 'K', ben: 'B' };

function buildItemToggle(li, item) {

    const persoon = item.toegewezen || 'geen'; // kleurvariant van .cat-toggle
    const letter = PERSOON_LETTER[persoon] || '';

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'cat-toggle flex-shrink-0' + (letter ? '' : ' cat-toggle-klein');
    btn.dataset.persoon = persoon;
    btn.textContent = letter;
    btn.title = 'Gedaan';
    setToggleState(btn, item.checked == 1);

    btn.addEventListener('click', () => {
        const nu = !btn.classList.contains('checked');
        setToggleState(btn, nu);
        li.dataset.checked = nu ? '1' : '0';
        autosaveChecked(li);
    });

    return btn;
}


// visuele status van één bolletje bijwerken (ingekleurde bol = afgevinkt)
function setToggleState(button, checked) {
    button.classList.toggle('checked', checked);
    button.setAttribute('aria-pressed', checked ? 'true' : 'false');
}


// Kaatje/Ben: gekleurd én vet. 'allebei' en geen toewijzing: originele kleur, normale dikte
function applyToegewezenStyle(label, waarde) {
    label.style.color = TOEGEWEZEN_KLEUREN[waarde] || '';
    label.style.fontWeight = TOEGEWEZEN_KLEUREN[waarde] ? 'bold' : '';
}


// DOM bouwen MET FETCH API
async function loadItems() {
    try {
        const response = await fetch('API/care_get_items.php');

        // tweede verdedigingslinie: sessie verlopen
        if (checkSession(response)) return;

        const data = await response.json();

        document.querySelectorAll('ul[id^="list_"]').forEach(ul => ul.innerHTML = '');

        data.forEach(item => {

            const list = document.getElementById('list_' + item.categorie);

            // onbekende categorie (bv. net toegevoegd door de ander): overslaan
            if (!list) return;

            list.appendChild(buildItemLi(item));
        });

    } catch (error) {
        console.error("Fout bij laden taken:", error);
        alert("Kon taken niet laden. Vernieuw de pagina.");
    }
}


// INIT CONTROLLER FLOW
async function initCamperCarePage() {
    await loadItems();
    initSlepen();
    initLiveUpdates();
}


// REALTIME UPDATES: periodiek verversen, zodat vinkjes die de ander op zijn/haar gsm zet getoond worden
const POLL_INTERVAL_MS = 4000; // elke 4 seconden
let modalOpenCount = 0; // niet verversen terwijl er een modal openstaat
let laatsteLokaleWijziging = 0; // niet verversen vlak na een eigen klik

function meldLokaleWijziging() {
    laatsteLokaleWijziging = Date.now();
}

async function pollForUpdates() {
    if (modalOpenCount > 0 || document.hidden || aanHetSlepen) return;
    if (Date.now() - laatsteLokaleWijziging < 1500) return;

    await loadItems();
}

function initLiveUpdates() {
    ['itemModal', 'categorieModal', 'nieuweCategorieModal'].forEach(id => {
        const modalEl = document.getElementById(id);
        modalEl.addEventListener('show.bs.modal', () => modalOpenCount++);
        modalEl.addEventListener('hidden.bs.modal', () => modalOpenCount--);
    });

    setInterval(pollForUpdates, POLL_INTERVAL_MS);

    // meteen verversen zodra het scherm weer actief wordt (bv. na ontgrendelen)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) pollForUpdates();
    });
}


// AUTOSAVE: vinkjes van één taak meteen opslaan
async function autosaveChecked(li) {

    meldLokaleWijziging();

    try {
        const result = await postJson('API/care_save_checked.php', {
            id: li.dataset.itemId,
            checked: li.dataset.checked === '1' ? 1 : 0,
            checked_kaatje: li.dataset.checkedKaatje === '1' ? 1 : 0,
            checked_ben: li.dataset.checkedBen === '1' ? 1 : 0
        });
        if (!result) return;

        if (!result.success) {
            alert("Kon wijziging niet opslaan: " + (result.message || result.error || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan vinkje:", error);
        alert("Kon wijziging niet opslaan. Probeer opnieuw.");
    }
}


// VOLGORDE: taken verslepen (SortableJS), ook naar een andere (opengeklapte) categorie
// gsm: taak even ingedrukt houden en dan schuiven | computer: meteen met de muis slepen
let aanHetSlepen = false; // niet verversen tijdens het slepen (anders springt de taak onder je vinger weg)

function initSlepen() {
    document.querySelectorAll('ul[id^="list_"]').forEach(ul => {
        Sortable.create(ul, {
            group: 'care',              // slepen tussen alle categorie-lijsten
            animation: 150,
            delay: 300,                 // eerst 0,3 s ingedrukt houden...
            delayOnTouchOnly: true,     // ...enkel op touchscreens; met de muis meteen
            touchStartThreshold: 5,     // kleine vingerbeweging tijdens het wachten = toch scrollen
            filter: '.cat-toggle, .item-menu', // tik op bolletje of ⋮ blijft gewoon werken
            preventOnFilter: false,
            onStart: () => { aanHetSlepen = true; },
            onEnd: bewaarVolgorde
        });
    });
}

// na loslaten: volgorde van de betrokken lijst(en) opslaan
async function bewaarVolgorde(event) {

    aanHetSlepen = false;

    if (event.from === event.to && event.oldIndex === event.newIndex) return; // niets veranderd

    meldLokaleWijziging();

    const lijsten = {};
    [event.from, event.to].forEach(ul => {
        const slug = ul.id.replace('list_', '');
        lijsten[slug] = Array.from(ul.children).map(li => li.dataset.itemId);
        ul.querySelectorAll('li').forEach(li => li.dataset.categorie = slug); // verplaatste taak hoort nu bij deze lijst
    });

    try {
        const result = await postJson('API/care_reorder.php', { lijsten: lijsten });
        if (!result) return;

        if (!result.success) {
            alert("Kon volgorde niet opslaan: " + (result.message || result.error || "Onbekende fout"));
            loadItems(); // scherm terug gelijkzetten met de database
        }

    } catch (error) {
        console.error("Fout bij opslaan volgorde:", error);
        alert("Kon volgorde niet opslaan. Probeer opnieuw.");
        loadItems();
    }
}


// MODAL: taak bewerken (naam, notitie, categorie, toewijzing) of verwijderen
let itemModalLi = null;

function openItemModal(li) {
    itemModalLi = li;
    document.getElementById('itemModalNaam').value = li.querySelector('.item-label').textContent;
    document.getElementById('itemModalNotitie').value = li.dataset.notitie || '';
    document.getElementById('itemModalCategorie').value = li.dataset.categorie || '';
    document.getElementById('itemModalToegewezen').value = li.dataset.toegewezen || '';
    new bootstrap.Modal(document.getElementById('itemModal')).show();
}


// MODAL: AUTOSAVE | naam en notitie bij blur van het veld, categorie/toewijzing meteen bij wijziging
async function saveItemModal() {

    if (!itemModalLi) return;

    const naam = document.getElementById('itemModalNaam').value.trim(); // trim() validatie
    const notitie = document.getElementById('itemModalNotitie').value.trim();

    if (!naam) { // lege input check
        alert("Voer een naam in");
        return;
    }

    if (!TAAK_NAAM_REGEX.test(naam)) {
        alert(TAAK_NAAM_FOUT);
        return;
    }

    const toegewezen = document.getElementById('itemModalToegewezen').value || null;
    const categorie = document.getElementById('itemModalCategorie').value;

    try {
        const result = await postJson('API/care_update_item.php', {
            id: itemModalLi.dataset.itemId,
            naam: naam,
            notitie: notitie,
            categorie: categorie,
            toegewezen: toegewezen
        });
        if (!result) return;

        if (result.success) {

            // <li> herbouwen met de nieuwe gegevens (aantal bolletjes, kleur en notitie volgen mee)
            const nieuweLi = buildItemLi({
                id: itemModalLi.dataset.itemId,
                naam: result.naam,
                notitie: result.notitie,
                categorie: result.categorie,
                checked: itemModalLi.dataset.checked === '1' ? 1 : 0,
                checked_kaatje: itemModalLi.dataset.checkedKaatje === '1' ? 1 : 0,
                checked_ben: itemModalLi.dataset.checkedBen === '1' ? 1 : 0,
                toegewezen: toegewezen
            });

            if (result.categorie !== itemModalLi.dataset.categorie) {
                // verplaatst naar een andere categorie: achteraan in de doellijst (zoals in de database)
                const doelLijst = document.getElementById('list_' + result.categorie);
                itemModalLi.remove();
                if (doelLijst) doelLijst.appendChild(nieuweLi);
            } else {
                itemModalLi.replaceWith(nieuweLi);
            }

            itemModalLi = nieuweLi;
        } else {
            alert("Kon taak niet opslaan: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan taak:", error);
        alert("Kon taak niet opslaan. Probeer opnieuw.");
    }
}

document.getElementById('itemModalNaam').addEventListener('blur', saveItemModal);
document.getElementById('itemModalNotitie').addEventListener('blur', saveItemModal);
document.getElementById('itemModalCategorie').addEventListener('change', saveItemModal);
document.getElementById('itemModalToegewezen').addEventListener('change', saveItemModal);


// MODAL: taak verwijderen
document.getElementById('btnItemVerwijder').addEventListener('click', async () => {

    if (!itemModalLi) return;

    const li = itemModalLi;
    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();

    if (!confirm('Deze taak definitief verwijderen?')) return;

    try {
        const result = await postJson('API/care_delete_item.php', { id: li.dataset.itemId });
        if (!result) return;

        if (result.success) {
            // opzoeken via id: de <li> kan intussen herbouwd zijn door de autosave bij blur van het naamveld
            const huidigeLi = document.querySelector('li[data-item-id="' + li.dataset.itemId + '"]');
            if (huidigeLi) huidigeLi.remove();
        } else {
            alert("Kon taak niet verwijderen: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij verwijderen taak:", error);
        alert("Kon taak niet verwijderen. Probeer opnieuw.");
    }
});


// TAAK TOEVOEGEN (herbruikbaar voor elke categorie-kaart)
function initAddItemHandler(button) {

    const categorie = button.dataset.categorie;
    const input = document.getElementById('item_' + categorie);

    async function voegToe() {

        const naam = input.value.trim(); // trim() validatie

        if (!naam) { // lege input check
            alert("Voer een taak in");
            return;
        }

        if (!TAAK_NAAM_REGEX.test(naam)) {
            alert(TAAK_NAAM_FOUT);
            return;
        }

        try {
            const result = await postJson('API/care_add_item.php', { naam: naam, categorie: categorie });
            if (!result) return;

            if (result.success) {
                document.getElementById('list_' + categorie).appendChild(buildItemLi({
                    id: result.id,
                    naam: result.naam,
                    notitie: null,
                    categorie: result.categorie,
                    checked: 0,
                    toegewezen: null
                }));

                input.value = '';
            } else {
                alert("Kon taak niet toevoegen: " + (result.message || "Onbekende fout"));
            }

        } catch (error) {
            console.error("Fout bij toevoegen taak:", error);
            alert("Kon taak niet toevoegen. Probeer opnieuw.");
        }
    }

    button.addEventListener('click', voegToe);

    // Enter in het invoerveld = toevoegen
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') voegToe();
    });
}

document.querySelectorAll('[id^="button-addon-"]').forEach(initAddItemHandler);


// CATEGORIE BEWERKEN: naam wijzigen, vinkjes resetten of categorie verwijderen (via modal)
let categorieModalSlug = null;     // slug van de categorie die bewerkt wordt
let categorieModalNaamHuidig = ''; // laatst opgeslagen naam (om overbodige saves te vermijden)

function openCategorieModal(button) {
    const card = button.closest('.card');
    categorieModalSlug = button.dataset.slug;
    categorieModalNaamHuidig = card.querySelector('.categorie-naam').textContent;
    document.getElementById('categorieModalNaam').value = categorieModalNaamHuidig;
    new bootstrap.Modal(document.getElementById('categorieModal')).show();
}


// AUTOSAVE categorie-naam (bij blur van het veld)
async function saveCategorieModal() {

    if (!categorieModalSlug) return;

    const naam = document.getElementById('categorieModalNaam').value.trim(); // trim() validatie

    if (!naam) { // lege input check
        alert("Voer een naam in");
        return;
    }

    if (!CATEGORIE_NAAM_REGEX.test(naam)) {
        alert(CATEGORIE_NAAM_FOUT);
        return;
    }

    if (naam === categorieModalNaamHuidig) return; // niets gewijzigd

    try {
        const result = await postJson('API/care_update_categorie.php', { slug: categorieModalSlug, naam: naam });
        if (!result) return;

        if (result.success) {
            categorieModalNaamHuidig = result.naam;

            // headerlabel bijwerken (veilig via textContent)
            const menuBtn = document.querySelector('.categorie-menu[data-slug="' + categorieModalSlug + '"]');
            if (menuBtn) menuBtn.closest('.card').querySelector('.categorie-naam').textContent = result.naam;

            // bijhorende optie in de taak-modal dropdown bijwerken
            const optie = document.querySelector('#itemModalCategorie option[value="' + categorieModalSlug + '"]');
            if (optie) optie.textContent = result.naam;
        } else {
            alert("Kon categorie niet opslaan: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan categorie:", error);
        alert("Kon categorie niet opslaan. Probeer opnieuw.");
    }
}

document.getElementById('categorieModalNaam').addEventListener('blur', saveCategorieModal);


// RESET PER KAART: alle vinkjes van deze categorie uitzetten
document.getElementById('btnCategorieReset').addEventListener('click', async () => {

    if (!categorieModalSlug) return;

    if (!confirm('Alle vinkjes van deze categorie uitzetten?')) return;

    try {
        const result = await postJson('API/care_reset_categorie.php', { slug: categorieModalSlug });
        if (!result) return;

        if (result.success) {
            document.querySelectorAll('#list_' + categorieModalSlug + ' li').forEach(li => {
                li.dataset.checked = '0';
                li.dataset.checkedKaatje = '0';
                li.dataset.checkedBen = '0';
                li.querySelectorAll('.cat-toggle').forEach(button => setToggleState(button, false));
            });
            bootstrap.Modal.getInstance(document.getElementById('categorieModal')).hide();
        } else {
            alert("Kon vinkjes niet uitzetten: " + (result.message || result.error || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij resetten categorie:", error);
        alert("Kon vinkjes niet uitzetten. Probeer opnieuw.");
    }
});


// CATEGORIE VERWIJDEREN (enkel als ze leeg is; backend blokkeert anders)
document.getElementById('btnCategorieVerwijder').addEventListener('click', async () => {

    if (!categorieModalSlug) return;

    if (!confirm('Deze categorie verwijderen?')) return;

    try {
        const result = await postJson('API/care_delete_categorie.php', { slug: categorieModalSlug });
        if (!result) return;

        if (result.success) {
            // kaart uit beeld halen + optie uit de taak-modal dropdown
            const menuBtn = document.querySelector('.categorie-menu[data-slug="' + categorieModalSlug + '"]');
            if (menuBtn) menuBtn.closest('.col-md-6').remove();

            const optie = document.querySelector('#itemModalCategorie option[value="' + categorieModalSlug + '"]');
            if (optie) optie.remove();

            bootstrap.Modal.getInstance(document.getElementById('categorieModal')).hide();
        } else {
            // o.a. 409: categorie bevat nog taken
            alert(result.message || "Kon categorie niet verwijderen.");
        }

    } catch (error) {
        console.error("Fout bij verwijderen categorie:", error);
        alert("Kon categorie niet verwijderen. Probeer opnieuw.");
    }
});


// ⋮-knopjes in de card headers koppelen (stopPropagation zodat de card niet in-/uitklapt)
document.querySelectorAll('.card-header .categorie-menu').forEach(button => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        openCategorieModal(button);
    });
});


// NIEUWE CATEGORIE toevoegen
document.getElementById('btnNieuweCategorie').addEventListener('click', () => {
    document.getElementById('nieuweCategorieNaam').value = '';
    new bootstrap.Modal(document.getElementById('nieuweCategorieModal')).show();
});

document.getElementById('btnNieuweCategorieOpslaan').addEventListener('click', async () => {

    const naam = document.getElementById('nieuweCategorieNaam').value.trim(); // trim() validatie

    if (!naam) { // lege input check
        alert("Voer een naam in");
        return;
    }

    if (!CATEGORIE_NAAM_REGEX.test(naam)) {
        alert(CATEGORIE_NAAM_FOUT);
        return;
    }

    try {
        const result = await postJson('API/care_add_categorie.php', { naam: naam });
        if (!result) return;

        if (result.success) {
            // pagina herladen zodat de nieuwe kaart + dropdown-optie server-side worden opgebouwd
            location.reload();
        } else {
            alert("Kon categorie niet toevoegen: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij toevoegen categorie:", error);
        alert("Kon categorie niet toevoegen. Probeer opnieuw.");
    }
});


</script>

<?php include 'footer.php';?>

</body>

</html>
