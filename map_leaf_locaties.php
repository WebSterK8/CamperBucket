

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<h2 class="text-darksage">Locaties per reis</h2>

<p class="small text-muted"></p>

<div class="d-flex flex-wrap align-items-end gap-3 my-3">

    <div class="flex-grow-1" style="min-width:220px;">
        <!--<label class="form-label mb-1" for="locatieReisSelect">*</label>-->
        <select class="form-select" id="locatieReisSelect"></select>
    </div>

    <button type="button" class="btn btn-outline-dark" id="btnNieuweLocatie" disabled>+&nbsp;Locatie toevoegen</button>

    <div class="btn-group" role="group" aria-label="Weergave">
        <button type="button" class="btn btn-outline-dark active" id="btnWeergaveKaart" title="Kaartweergave" aria-label="Kaartweergave">▦</button>
        <button type="button" class="btn btn-outline-dark" id="btnWeergaveLijst" title="Lijstweergave" aria-label="Lijstweergave">☰</button>
    </div>

</div>

<div id="mapLocaties"></div>

<div id="locatieLijst" class="list-group" style="display:none;"></div>

<div id="locatieCategorieLegenda" class="d-flex flex-wrap align-items-center gap-3 mt-3">
    <!-- categorie-chips worden via JS gevuld -->
    <button type="button" class="btn btn-sm btn-outline-dark" id="btnNieuweLocatieCategorie">+&nbsp;Categorie</button>
</div>


<!-- Modal locatie toevoegen / bewerken -->
<div class="modal fade" id="locatieModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold" id="locatieModalTitel">Locatie toevoegen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="locatieId">

                <label class="form-label" for="locatieModalReis">Reis *</label>
                <select class="form-select mb-3" id="locatieModalReis"></select>

                <label class="form-label" for="locatieCategorie">Categorie *</label>
                <select class="form-select mb-3" id="locatieCategorie"></select>

                <label class="form-label" for="locatieNaam">Naam *</label>
                <input class="form-control mb-3" type="text" id="locatieNaam" maxlength="100" pattern="[a-zA-ZÀ-ÿ0-9\s\-',\.]+" required>

                <label class="form-label" for="locatieBeschrijving">Beschrijving / opmerkingen</label>
                <textarea class="form-control mb-3" id="locatieBeschrijving" maxlength="1000" rows="3"></textarea>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="locatieLat">Breedtegraad (lat) *</label>
                        <input class="form-control mb-3" type="number" step="any" min="-90" max="90" id="locatieLat" required>
                    </div>
                    <div class="col">
                        <label class="form-label" for="locatieLon">Lengtegraad (lon) *</label>
                        <input class="form-control mb-3" type="number" step="any" min="-180" max="180" id="locatieLon" required>
                    </div>
                </div>

                <label class="form-label" for="locatieLink">Link (waar gevonden?)</label>
                <input class="form-control mb-2" type="url" id="locatieLink" maxlength="500" placeholder="https://...">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" id="btnLocatieOpslaan">Opslaan</button>
                <button type="button" class="btn btn-outline-danger" id="btnLocatieVerwijder" style="display:none;">Verwijderen</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
            </div>

        </div>
    </div>
</div>


<!-- Modal categorie bewerken / verwijderen -->
<div class="modal fade" id="locatieCategorieModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Categorie bewerken</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="locatieCategorieModalNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="locatieCategorieModalNaam" maxlength="50" pattern="[a-zA-ZÀ-ÿ\s\-'&]+">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-danger" id="btnLocatieCategorieVerwijder">Verwijderen</button>
            </div>

        </div>
    </div>
</div>


<!-- Modal nieuwe categorie -->
<div class="modal fade" id="nieuweLocatieCategorieModal" tabindex="-1">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold">Nieuwe categorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label" for="nieuweLocatieCategorieNaam">Naam</label>
                <input class="form-control mb-3" type="text" id="nieuweLocatieCategorieNaam" maxlength="50" pattern="[a-zA-ZÀ-ÿ\s\-'&]+" placeholder="Bijv. Uitzichtpunt">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-outline-dark" id="btnNieuweLocatieCategorieOpslaan">Toevoegen</button>
            </div>

        </div>
    </div>
</div>


<script>

const DEBUG = false;

// kleurenpalet voor de categorie-bolletjes op de kaart, in dezelfde familie als de site-kleuren
const LOCATIE_KLEUREN = ['#7a8f7a', '#669999', '#c98a4b', '#9b6b9e', '#4b7bac', '#b5651d', '#5a8f9e', '#a1866f'];

let categorieen = [];          // [{slug, naam, positie}], op volgorde van positie
let categorieKleur = {};       // slug -> hexkleur

let reizenLijst = [];          // [{id, land, ...}], voor de reis-dropdown in het locatie-formulier
let huidigeReisId = null;
let locatieMarkers = [];       // huidige markers op de kaart
let tempMarker = null;         // tijdelijke marker terwijl het formulier open staat
let locatieModalOpen = false;  // bepaalt of een kaartklik het open formulier bijwerkt


// =========================
// Kaart instellen (Leaflet)
// =========================

const mapLocaties = L.map("mapLocaties").setView([51.05, 3.73], 9); // België

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "© OpenStreetMap"
}).addTo(mapLocaties);


// bolvormig icoon per categorie (zelfde visuele taal als de ronde toggles op de Checklist-pagina)
function maakLocatieIcon(kleur) {
    return L.divIcon({
        className: 'locatie-marker',
        html: '<span class="locatie-marker-dot" style="background:' + kleur + ';"></span>',
        iconSize: [18, 18],
        iconAnchor: [9, 9],
        popupAnchor: [0, -9]
    });
}


// =========================
// CATEGORIEËN
// =========================

async function loadCategorieen() {
    try {
        const response = await fetch('API/get_locatie_categorieen.php');
        if (checkSession(response)) return;

        categorieen = await response.json();

        categorieKleur = {};
        categorieen.forEach((cat, index) => {
            categorieKleur[cat.slug] = LOCATIE_KLEUREN[index % LOCATIE_KLEUREN.length];
        });

        vulCategorieSelect();
        bouwLegenda();

    } catch (error) {
        console.error("Fout bij laden categorieën:", error);
    }
}


// dropdown in het locatie-formulier vullen
function vulCategorieSelect() {
    const select = document.getElementById('locatieCategorie');
    const huidigeWaarde = select.value;
    select.innerHTML = '';

    categorieen.forEach(cat => {
        const optie = document.createElement('option');
        optie.value = cat.slug;
        optie.textContent = cat.naam;
        select.appendChild(optie);
    });

    const nieuweOptie = document.createElement('option');
    nieuweOptie.value = '__nieuw__';
    nieuweOptie.textContent = '-- Nieuwe categorie --';
    select.appendChild(nieuweOptie);

    if (huidigeWaarde && categorieen.some(c => c.slug === huidigeWaarde)) {
        select.value = huidigeWaarde;
    }
}


// kiezen van "-- Nieuwe categorie --": locatieModal even opzij zetten en het categorie-formulier tonen
let heropenLocatieModalNaCategorie = false;

document.getElementById('locatieCategorie').addEventListener('change', function() {
    if (this.value !== '__nieuw__') return;

    heropenLocatieModalNaCategorie = true;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('locatieModal')).hide();

    document.getElementById('nieuweLocatieCategorieNaam').value = '';
    new bootstrap.Modal(document.getElementById('nieuweLocatieCategorieModal')).show();
});

// zodra het categorie-formulier sluit (opgeslagen of geannuleerd): terug naar het locatie-formulier
document.getElementById('nieuweLocatieCategorieModal').addEventListener('hidden.bs.modal', () => {
    if (!heropenLocatieModalNaCategorie) return;
    heropenLocatieModalNaCategorie = false;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('locatieModal')).show();
});


// legenda met kleurbolletje + naam + bewerk-knopje per categorie
function bouwLegenda() {
    const legenda = document.getElementById('locatieCategorieLegenda');
    const btnNieuw = document.getElementById('btnNieuweLocatieCategorie');

    legenda.querySelectorAll('.locatie-legenda-item').forEach(el => el.remove());

    categorieen.forEach(cat => {
        const chip = document.createElement('span');
        chip.className = 'locatie-legenda-item d-flex align-items-center gap-2';

        const bol = document.createElement('span');
        bol.className = 'locatie-marker-dot d-inline-block';
        bol.style.background = categorieKleur[cat.slug];

        const label = document.createElement('span');
        label.className = 'small';
        label.textContent = cat.naam;

        const bewerkBtn = document.createElement('button');
        bewerkBtn.type = 'button';
        bewerkBtn.className = 'btn btn-sm categorie-menu';
        bewerkBtn.title = 'Categorie bewerken';
        bewerkBtn.textContent = '⋮';
        bewerkBtn.addEventListener('click', () => openLocatieCategorieModal(cat.slug, cat.naam));

        chip.appendChild(bol);
        chip.appendChild(label);
        chip.appendChild(bewerkBtn);

        legenda.insertBefore(chip, btnNieuw);
    });
}


let locatieCategorieModalSlug = null;
let locatieCategorieModalNaamHuidig = '';

function openLocatieCategorieModal(slug, naam) {
    locatieCategorieModalSlug = slug;
    locatieCategorieModalNaamHuidig = naam;
    document.getElementById('locatieCategorieModalNaam').value = naam;
    new bootstrap.Modal(document.getElementById('locatieCategorieModal')).show();
}


// AUTOSAVE categorie-naam bij blur, zelfde stijl als bij de Checklist
async function saveLocatieCategorieModal() {

    if (!locatieCategorieModalSlug) return;

    const naam = document.getElementById('locatieCategorieModalNaam').value.trim();

    if (!naam) {
        alert("Voer een naam in");
        return;
    }

    if (!/^[a-zA-ZÀ-ÿ\s\-'&]+$/.test(naam)) {
        alert("Alleen letters, spaties, koppeltekens, apostrofs en & zijn toegestaan");
        return;
    }

    if (naam === locatieCategorieModalNaamHuidig) return;

    try {
        const response = await fetch('API/update_locatie_categorie.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slug: locatieCategorieModalSlug, naam: naam })
        });

        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            locatieCategorieModalNaamHuidig = result.naam;
            await loadCategorieen();
            await loadLocaties();
        } else {
            alert("Kon categorie niet opslaan: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij opslaan categorie:", error);
        alert("Kon categorie niet opslaan. Probeer opnieuw.");
    }
}

document.getElementById('locatieCategorieModalNaam').addEventListener('blur', saveLocatieCategorieModal);


document.getElementById('btnLocatieCategorieVerwijder').addEventListener('click', async () => {

    if (!locatieCategorieModalSlug) return;

    if (!confirm('Deze categorie verwijderen?')) return;

    try {
        const response = await fetch('API/delete_locatie_categorie.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slug: locatieCategorieModalSlug })
        });

        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('locatieCategorieModal')).hide();
            await loadCategorieen();
        } else {
            // o.a. 409: categorie bevat nog locaties
            alert(result.message || "Kon categorie niet verwijderen.");
        }

    } catch (error) {
        console.error("Fout bij verwijderen categorie:", error);
        alert("Kon categorie niet verwijderen. Probeer opnieuw.");
    }
});


document.getElementById('btnNieuweLocatieCategorie').addEventListener('click', () => {
    document.getElementById('nieuweLocatieCategorieNaam').value = '';
    new bootstrap.Modal(document.getElementById('nieuweLocatieCategorieModal')).show();
});

document.getElementById('btnNieuweLocatieCategorieOpslaan').addEventListener('click', async () => {

    const naam = document.getElementById('nieuweLocatieCategorieNaam').value.trim();

    if (!naam) {
        alert("Voer een naam in");
        return;
    }

    if (!/^[a-zA-ZÀ-ÿ\s\-'&]+$/.test(naam)) {
        alert("Alleen letters, spaties, koppeltekens, apostrofs en & zijn toegestaan");
        return;
    }

    try {
        const response = await fetch('API/add_locatie_categorie.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ naam: naam })
        });

        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            await loadCategorieen();
            document.getElementById('locatieCategorie').value = result.slug;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('nieuweLocatieCategorieModal')).hide();
        } else {
            alert("Kon categorie niet toevoegen: " + (result.message || "Onbekende fout"));
        }

    } catch (error) {
        console.error("Fout bij toevoegen categorie:", error);
        alert("Kon categorie niet toevoegen. Probeer opnieuw.");
    }
});


// =========================
// REIZEN (dropdown)
// =========================

// label voor de dropdown: bestemming + datum (zelfde opbouw als de datumweergave op de BucketList-pagina)
function maakReisLabel(reis) {
    let label = reis.land || '(geen bestemming)';

    if (reis.start_jaar) {
        let datumTekst = reis.start_jaar;
        if (reis.start_maand) datumTekst = reis.start_maand + '/' + datumTekst;
        if (reis.eind_jaar) datumTekst += ' – ' + reis.eind_jaar;
        label += ' (' + datumTekst + ')';
    }

    return label;
}


// datum bouwen uit (mogelijk onvolledige) jaar/maand/dag-velden; bij een einddatum vult
// een ontbrekende maand/dag aan met het laatste mogelijke moment, zodat de reis niet te vroeg
// als "voorbij" beschouwd wordt
function maakDatum(jaar, maand, dag, isEind) {
    if (!jaar) return null;
    const m = maand ? maand - 1 : (isEind ? 11 : 0);
    const d = dag || (isEind ? 31 : 1);
    return new Date(jaar, m, d);
}


// eerste reis in de (chronologisch gesorteerde) lijst die nog loopt of nog moet komen;
// zonder match (alle reizen liggen in het verleden): de meest recente reis als terugval
function kiesStandaardReis(reizen) {
    const vandaag = new Date();
    vandaag.setHours(0, 0, 0, 0);

    for (const reis of reizen) {
        const eind = maakDatum(reis.eind_jaar, reis.eind_maand, reis.eind_dag, true)
                  || maakDatum(reis.start_jaar, reis.start_maand, reis.start_dag, true);
        if (eind && eind >= vandaag) return reis.id;
    }

    return reizen[reizen.length - 1].id;
}


async function loadReizen() {
    try {
        const response = await fetch('API/get_reizen.php');
        if (checkSession(response)) return;

        reizenLijst = (await response.json()).filter(r => r.intro == 0);

        const select = document.getElementById('locatieReisSelect');
        select.innerHTML = '';

        if (reizenLijst.length === 0) {
            const optie = document.createElement('option');
            optie.textContent = 'Geen reizen gevonden';
            select.appendChild(optie);
            document.getElementById('btnNieuweLocatie').disabled = true;
            return;
        }

        reizenLijst.forEach(reis => {
            const optie = document.createElement('option');
            optie.value = reis.id;
            optie.textContent = maakReisLabel(reis);
            select.appendChild(optie);
        });

        const reisIdUitUrl = new URLSearchParams(window.location.search).get('reis_id');
        const geldigeReisId = reisIdUitUrl && reizenLijst.some(r => String(r.id) === reisIdUitUrl) ? reisIdUitUrl : null;

        huidigeReisId = geldigeReisId || kiesStandaardReis(reizenLijst);
        select.value = huidigeReisId;
        document.getElementById('btnNieuweLocatie').disabled = false;

        select.addEventListener('change', () => {
            huidigeReisId = select.value;
            loadLocaties();
        });

    } catch (error) {
        console.error("Fout bij laden reizen:", error);
        alert("Kon reizen niet laden. Vernieuw de pagina.");
    }
}


// dezelfde reizenlijst gebruiken om de reis-dropdown in het locatie-formulier te vullen
function vulReisSelect() {
    const select = document.getElementById('locatieModalReis');
    select.innerHTML = '';

    reizenLijst.forEach(reis => {
        const optie = document.createElement('option');
        optie.value = reis.id;
        optie.textContent = maakReisLabel(reis);
        select.appendChild(optie);
    });
}


// =========================
// LOCATIES OP DE KAART
// =========================

async function loadLocaties() {

    locatieMarkers.forEach(marker => mapLocaties.removeLayer(marker));
    locatieMarkers = [];

    if (!huidigeReisId) return;

    try {
        const response = await fetch('API/get_locaties.php?reis_id=' + encodeURIComponent(huidigeReisId));
        if (checkSession(response)) return;

        const locaties = await response.json();

        locaties.forEach(locatie => {
            const kleur = categorieKleur[locatie.categorie] || LOCATIE_KLEUREN[0];
            const marker = L.marker([locatie.lat, locatie.lon], { icon: maakLocatieIcon(kleur) }).addTo(mapLocaties);
            marker.bindPopup(maakPopupContent(locatie));
            locatieMarkers.push(marker);
        });

        renderLocatieLijst(locaties);

    } catch (error) {
        console.error("Fout bij laden locaties:", error);
        alert("Kon locaties niet laden. Vernieuw de pagina.");
    }
}


// gedeelde inhoud (naam, categorie, link) voor zowel de kaart-popup als de lijstweergave
function maakLocatieDetails(locatie) {

    const wrapper = document.createElement('div');

    const titel = document.createElement('b');
    titel.textContent = locatie.naam;
    wrapper.appendChild(titel);

    const categorieNaam = categorieen.find(c => c.slug === locatie.categorie);
    const catP = document.createElement('div');
    catP.className = 'small text-muted';
    catP.textContent = categorieNaam ? categorieNaam.naam : locatie.categorie;
    wrapper.appendChild(catP);

    if (locatie.link) {
        const link = document.createElement('a');
        link.href = locatie.link;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.textContent = 'Bron openen ↗';
        link.className = 'd-block small mt-1';
        wrapper.appendChild(link);
    }

    return wrapper;
}


// gedeelde Bewerken/Verwijderen-knoppen voor zowel de kaart-popup als de lijstweergave
function maakActieKnoppen(locatie) {

    const knoppen = document.createElement('div');
    knoppen.className = 'd-flex gap-2 mt-2';

    const bewerkBtn = document.createElement('button');
    bewerkBtn.type = 'button';
    bewerkBtn.className = 'btn btn-sm btn-outline-dark';
    bewerkBtn.textContent = 'Bewerken';
    bewerkBtn.addEventListener('click', () => openLocatieModal(locatie));

    const verwijderBtn = document.createElement('button');
    verwijderBtn.type = 'button';
    verwijderBtn.className = 'btn btn-sm btn-outline-danger';
    verwijderBtn.textContent = 'Verwijderen';
    verwijderBtn.addEventListener('click', () => verwijderLocatie(locatie.id));

    knoppen.appendChild(bewerkBtn);
    knoppen.appendChild(verwijderBtn);

    return knoppen;
}


// popup-inhoud veilig opbouwen (DOM + textContent, geen HTML-string van gebruikersinput)
function maakPopupContent(locatie) {
    const wrapper = maakLocatieDetails(locatie);
    wrapper.appendChild(maakActieKnoppen(locatie));
    return wrapper;
}


// lijstweergave (alternatief voor de kaart) met dezelfde locaties
function renderLocatieLijst(locaties) {

    const container = document.getElementById('locatieLijst');
    container.innerHTML = '';

    if (locaties.length === 0) {
        const leeg = document.createElement('p');
        leeg.className = 'text-muted small mb-0 p-2';
        leeg.textContent = 'Nog geen locaties opgeslagen voor deze reis.';
        container.appendChild(leeg);
        return;
    }

    locaties.forEach(locatie => {
        const item = document.createElement('div');
        item.className = 'list-group-item d-flex align-items-start gap-3';

        const bol = document.createElement('span');
        bol.className = 'locatie-marker-dot flex-shrink-0 mt-1';
        bol.style.background = categorieKleur[locatie.categorie] || LOCATIE_KLEUREN[0];

        const inhoud = document.createElement('div');
        inhoud.className = 'flex-grow-1';
        inhoud.appendChild(maakLocatieDetails(locatie));

        item.appendChild(bol);
        item.appendChild(inhoud);
        item.appendChild(maakActieKnoppen(locatie));

        container.appendChild(item);
    });
}


// weergave wisselen tussen kaart en lijst
function setWeergave(modus) {

    const isKaart = modus === 'kaart';

    document.getElementById('mapLocaties').style.display = isKaart ? '' : 'none';
    document.getElementById('locatieLijst').style.display = isKaart ? 'none' : '';

    document.getElementById('btnWeergaveKaart').classList.toggle('active', isKaart);
    document.getElementById('btnWeergaveLijst').classList.toggle('active', !isKaart);

    // Leaflet moet zijn afmetingen herberekenen nadat de container terug zichtbaar wordt
    if (isKaart) setTimeout(() => mapLocaties.invalidateSize(), 0);
}

document.getElementById('btnWeergaveKaart').addEventListener('click', () => setWeergave('kaart'));
document.getElementById('btnWeergaveLijst').addEventListener('click', () => setWeergave('lijst'));


// =========================
// LOCATIE TOEVOEGEN / BEWERKEN
// =========================

let locatieModalId = null; // leeg = nieuwe locatie, anders bewerken

function openLocatieModal(locatie = null) {

    locatieModalId = locatie ? locatie.id : null;

    document.getElementById('locatieModalTitel').textContent = locatie ? 'Locatie bewerken' : 'Locatie toevoegen';
    document.getElementById('locatieId').value = locatie ? locatie.id : '';

    vulReisSelect();
    document.getElementById('locatieModalReis').value = locatie ? locatie.reis_id : huidigeReisId;

    document.getElementById('locatieNaam').value = locatie ? locatie.naam : '';
    document.getElementById('locatieBeschrijving').value = locatie ? (locatie.beschrijving || '') : '';
    document.getElementById('locatieLat').value = locatie ? locatie.lat : '';
    document.getElementById('locatieLon').value = locatie ? locatie.lon : '';
    document.getElementById('locatieLink').value = locatie ? (locatie.link || '') : '';
    document.getElementById('btnLocatieVerwijder').style.display = locatie ? 'inline-block' : 'none';

    vulCategorieSelect();
    if (locatie) document.getElementById('locatieCategorie').value = locatie.categorie;

    mapLocaties.closePopup();

    if (locatie) {
        plaatsTempMarker(locatie.lat, locatie.lon);
    } else if (tempMarker) {
        mapLocaties.removeLayer(tempMarker);
        tempMarker = null;
    }

    new bootstrap.Modal(document.getElementById('locatieModal')).show();
}


function plaatsTempMarker(lat, lon) {
    if (tempMarker) mapLocaties.removeLayer(tempMarker);
    tempMarker = L.circleMarker([lat, lon], {
        radius: 9,
        color: '#606f60',
        weight: 2,
        fillColor: '#606f60',
        fillOpacity: 0.35
    }).addTo(mapLocaties);
}


document.getElementById('btnNieuweLocatie').addEventListener('click', () => openLocatieModal());


// klik op de kaart: coördinaten invullen in het open formulier (of een nieuw formulier openen)
mapLocaties.on('click', function(e) {

    const lat = e.latlng.lat;
    const lon = e.latlng.lng;

    plaatsTempMarker(lat, lon);

    if (locatieModalOpen) {
        document.getElementById('locatieLat').value = lat.toFixed(6);
        document.getElementById('locatieLon').value = lon.toFixed(6);
    } else {
        openLocatieModal();
        document.getElementById('locatieLat').value = lat.toFixed(6);
        document.getElementById('locatieLon').value = lon.toFixed(6);
    }
});


document.getElementById('btnLocatieOpslaan').addEventListener('click', async () => {

    const reisId = document.getElementById('locatieModalReis').value;
    const naam = document.getElementById('locatieNaam').value.trim();
    const beschrijving = document.getElementById('locatieBeschrijving').value.trim();
    const categorie = document.getElementById('locatieCategorie').value;
    const lat = document.getElementById('locatieLat').value.trim();
    const lon = document.getElementById('locatieLon').value.trim();
    const link = document.getElementById('locatieLink').value.trim();

    if (!reisId) { alert('Kies een reis.'); return; }

    if (!naam) { alert('Naam is verplicht.'); return; }

    if (!/^[a-zA-ZÀ-ÿ0-9\s\-',\.]+$/.test(naam)) {
        alert("Naam: enkel letters, cijfers, spaties, koppeltekens, komma's of punten.");
        return;
    }

    if (!categorie || categorie === '__nieuw__') { alert('Kies een categorie.'); return; }

    if (lat === '' || lon === '') { alert('Klik op de kaart of vul de coördinaten manueel in.'); return; }

    const payload = {
        reis_id: reisId,
        naam: naam,
        beschrijving: beschrijving,
        categorie: categorie,
        lat: parseFloat(lat),
        lon: parseFloat(lon),
        link: link
    };

    if (locatieModalId) payload.id = locatieModalId;

    try {
        const response = await fetch(locatieModalId ? 'API/update_locatie.php' : 'API/add_locatie.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('locatieModal')).hide();
            loadLocaties();
        } else {
            alert('Kon locatie niet opslaan: ' + (result.message || 'Onbekende fout'));
        }

    } catch (error) {
        console.error('Fout bij opslaan locatie:', error);
        alert('Kon locatie niet opslaan. Probeer opnieuw.');
    }
});


document.getElementById('btnLocatieVerwijder').addEventListener('click', () => {
    if (!locatieModalId) return;
    bootstrap.Modal.getInstance(document.getElementById('locatieModal')).hide();
    verwijderLocatie(locatieModalId);
});


async function verwijderLocatie(id) {

    if (!confirm('Deze locatie definitief verwijderen?')) return;

    try {
        const response = await fetch('API/delete_locatie.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });

        if (checkSession(response)) return;

        const result = await response.json();

        if (result.success) {
            loadLocaties();
        } else {
            alert('Kon locatie niet verwijderen: ' + (result.message || 'Onbekende fout'));
        }

    } catch (error) {
        console.error('Fout bij verwijderen locatie:', error);
        alert('Kon locatie niet verwijderen. Probeer opnieuw.');
    }
}


// modal-status bijhouden: klikken op de kaart terwijl het formulier open staat werkt de velden bij i.p.v. een nieuw formulier te openen
document.getElementById('locatieModal').addEventListener('show.bs.modal', () => { locatieModalOpen = true; });
document.getElementById('locatieModal').addEventListener('hidden.bs.modal', () => {
    locatieModalOpen = false;
    if (tempMarker) {
        mapLocaties.removeLayer(tempMarker);
        tempMarker = null;
    }
});


// =========================
// INIT
// =========================

async function initLocatiesKaart() {
    await loadCategorieen();
    await loadReizen();
    await loadLocaties();
}

initLocatiesKaart();

</script>
