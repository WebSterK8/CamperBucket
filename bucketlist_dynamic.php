<?php
require_once 'dbconnect.php';
require_once 'controlelogin.php';
?>

<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>BucketList</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="custom.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

</head>


<body>

<div class="container-fluid mt-3">
    <?php include 'header.php';?>
    <?php include 'navbar.php';?>
</div>


<!-- Sectie 1: Timeline -->
<section class="container-lg mt-2 fade-section">
    <?php include 'timeline.php';?>
</section>


<!-- Sectie 2: Reizen (dynamisch) -->
<section class="container-lg mt-2 fade-section">

    <div class="d-flex justify-content-between align-items-center m-5">
        <h1 style="color: #606f60;">Reizen</h1>
        <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#reizenModal">
            + Reis toevoegen
        </button>
    </div>

    <div class="row gx-3 gy-3" id="reizenGrid"></div>

</section>


<!-- Modal toevoegen / bewerken -->
<div class="modal fade" id="reizenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-alfasage">
                <h5 class="modal-title text-darksage fw-bold" id="modalTitel">Reis toevoegen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reisId">
                <label class="form-label" for="reizenLand">Bestemming *</label>
                <input class="form-control mb-2" type="text" id="reizenLand" maxlength="100" pattern="[a-zA-ZÀ-ÿ0-9\s\-',\.]+" required>
                <label class="form-label" for="reizenBeschrijving">Beschrijving</label>
                <textarea class="form-control mb-2" id="reizenBeschrijving" maxlength="500" rows="3"></textarea>
                <label class="form-label" for="reizenFoto">Foto (pad)</label>
                <input class="form-control mb-2" type="text" id="reizenFoto" maxlength="500">
                <label class="form-label" for="reizenFotoAlt">Foto alt-tekst</label>
                <input class="form-control mb-3" type="text" id="reizenFotoAlt" maxlength="255">
                <div class="row">
                    <div class="col">
                        <label class="form-label">Startjaar</label>
                        <input class="form-control" type="text" id="startJaar" maxlength="4" pattern="[0-9]{4}">
                    </div>
                    <div class="col">
                        <label class="form-label">Maand</label>
                        <input class="form-control" type="number" id="startMaand" min="1" max="12">
                    </div>
                    <div class="col">
                        <label class="form-label">Dag</label>
                        <input class="form-control" type="number" id="startDag" min="1" max="31">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <label class="form-label">Eindjaar</label>
                        <input class="form-control" type="text" id="eindJaar" maxlength="4" pattern="[0-9]{4}">
                    </div>
                    <div class="col">
                        <label class="form-label">Maand</label>
                        <input class="form-control" type="number" id="eindMaand" min="1" max="12">
                    </div>
                    <div class="col">
                        <label class="form-label">Dag</label>
                        <input class="form-control" type="number" id="eindDag" min="1" max="31">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" id="btnOpslaan">Opslaan</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php';?>


<!-- fade effect -->
<script>
gsap.utils.toArray(".fade-section").forEach((section, index, sections) => {
    const nextSection = sections[index + 1];
    if (nextSection) {
        ScrollTrigger.create({
            trigger: section,
            start: "top top",
            end: "bottom bottom",
            scrub: true,
            onUpdate: (self) => {
                const progress = self.progress;
                gsap.to(section, { opacity: 1 - progress, overwrite: true, ease: "power1.out" }); // Vervagen
                gsap.to(nextSection, { opacity: progress, overwrite: true, ease: "power1.in" }); // Verschijnen
            },
        });
    }
});
</script>


<script>

const DEBUG = false;

document.addEventListener('DOMContentLoaded', loadReizen); // voer functie uit wanneer de HTML pagina geladen is


// REIZEN OPHALEN EN KAARTEN BOUWEN MET FETCH API
async function loadReizen() {
    try {
        const response = await fetch('API/get_reizen.php');

        // tweede verdedigingslinie: sessie verlopen
        if (response.status === 401) { window.location.href = 'login.php'; return; }

        const data = await response.json();

        const grid = document.getElementById('reizenGrid');
        grid.innerHTML = '';

        // intro-rij weggfilteren, enkel gewone reizen tonen
        data.filter(r => r.intro == 0).forEach(reis => grid.appendChild(maakKaart(reis)));

    } catch (error) {
        console.error('Fout bij laden reizen:', error);
        alert('Kon reizen niet laden. Vernieuw de pagina.');
    }
}


// KAART BOUWEN (DOM - veilig via createElement + textContent)
function maakKaart(reis) {

    const col = document.createElement('div');
    col.className = 'col-12 col-md-4';
    col.id = 'reis-' + reis.id;

    const card = document.createElement('div');
    card.className = 'card h-100 shadow-sm';

    // card-header: bestemming
    const header = document.createElement('div');
    header.className = 'card-header bg-alfasage text-darksage fw-bold';
    header.textContent = reis.land ?? '(geen bestemming)'; // veilig door textContent

    // card-body: foto, beschrijving, datum
    const body = document.createElement('div');
    body.className = 'card-body';

    if (reis.foto) {
        const img = document.createElement('img');
        img.src = reis.foto;
        img.alt = reis.foto_alt ?? '';
        img.className = 'img-fluid rounded mb-2';
        body.appendChild(img);
    }

    if (reis.beschrijving) {
        const p = document.createElement('p');
        p.className = 'card-text';
        p.textContent = reis.beschrijving; // veilig door textContent
        body.appendChild(p);
    }

    if (reis.start_jaar) {
        const datum = document.createElement('small');
        datum.className = 'text-muted d-block';
        let datumTekst = reis.start_jaar;
        if (reis.start_maand) datumTekst = reis.start_maand + '/' + datumTekst;
        if (reis.eind_jaar) datumTekst += ' – ' + reis.eind_jaar;
        datum.textContent = datumTekst; // veilig door textContent
        body.appendChild(datum);
    }

    // card-footer: bewerken + verwijderen
    const footer = document.createElement('div');
    footer.className = 'card-footer d-flex justify-content-end gap-2';

    const btnBewerk = document.createElement('button');
    btnBewerk.className = 'btn btn-outline-dark btn-sm';
    btnBewerk.textContent = 'Bewerken';
    btnBewerk.addEventListener('click', () => openModal(reis));

    const btnVerwijder = document.createElement('button');
    btnVerwijder.className = 'btn btn-outline-danger btn-sm';
    btnVerwijder.textContent = 'Verwijderen';
    btnVerwijder.addEventListener('click', () => verwijderReis(reis.id));

    footer.appendChild(btnBewerk);
    footer.appendChild(btnVerwijder);

    card.appendChild(header);
    card.appendChild(body);
    card.appendChild(footer);
    col.appendChild(card);

    return col;
}


// MODAL OPENEN (toevoegen of bewerken)
function openModal(reis = null) {
    document.getElementById('reisId').value              = reis ? reis.id            : '';
    document.getElementById('modalTitel').textContent    = reis ? 'Reis bewerken'    : 'Reis toevoegen';
    document.getElementById('reizenLand').value          = reis ? (reis.land          ?? '') : '';
    document.getElementById('reizenBeschrijving').value  = reis ? (reis.beschrijving  ?? '') : '';
    document.getElementById('reizenFoto').value          = reis ? (reis.foto          ?? '') : '';
    document.getElementById('reizenFotoAlt').value       = reis ? (reis.foto_alt      ?? '') : '';
    document.getElementById('startJaar').value           = reis ? (reis.start_jaar    ?? '') : '';
    document.getElementById('startMaand').value          = reis ? (reis.start_maand   ?? '') : '';
    document.getElementById('startDag').value            = reis ? (reis.start_dag     ?? '') : '';
    document.getElementById('eindJaar').value            = reis ? (reis.eind_jaar     ?? '') : '';
    document.getElementById('eindMaand').value           = reis ? (reis.eind_maand    ?? '') : '';
    document.getElementById('eindDag').value             = reis ? (reis.eind_dag      ?? '') : '';

    new bootstrap.Modal(document.getElementById('reizenModal')).show();
}


// REIS OPSLAAN (toevoegen of bewerken) MET FETCH API
document.getElementById('btnOpslaan').addEventListener('click', async () => {

    const id   = document.getElementById('reisId').value;
    const land = document.getElementById('reizenLand').value.trim(); // trim() validatie

    if (!land) { alert('Bestemming is verplicht.'); return; }

    const data = {
        land:         land,
        beschrijving: document.getElementById('reizenBeschrijving').value.trim(),
        foto:         document.getElementById('reizenFoto').value.trim(),
        foto_alt:     document.getElementById('reizenFotoAlt').value.trim(),
        start_jaar:   document.getElementById('startJaar').value.trim(),
        start_maand:  document.getElementById('startMaand').value.trim(),
        start_dag:    document.getElementById('startDag').value.trim(),
        eind_jaar:    document.getElementById('eindJaar').value.trim(),
        eind_maand:   document.getElementById('eindMaand').value.trim(),
        eind_dag:     document.getElementById('eindDag').value.trim()
    };

    if (id) data.id = id;

    try {
        const response = await fetch(id ? 'API/update_reis.php' : 'API/add_reis.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, // beveiliging data-overdracht
            body: JSON.stringify(data)
        });

        // tweede verdedigingslinie: sessie verlopen
        if (response.status === 401) { window.location.href = 'login.php'; return; }

        const result = await response.json();

        if (response.ok) {
            bootstrap.Modal.getInstance(document.getElementById('reizenModal')).hide();
            loadReizen(); // kaarten herladen
        } else {
            alert(result.message || 'Onbekende fout');
        }

    } catch (error) {
        console.error('Fout bij opslaan:', error);
        alert('Kon reis niet opslaan. Probeer opnieuw.');
    }
});


// REIS VERWIJDEREN MET FETCH API
async function verwijderReis(id) {

    if (!confirm('Ben je zeker dat je deze reis wil verwijderen?')) return;

    try {
        const response = await fetch('API/delete_reis.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });

        // tweede verdedigingslinie: sessie verlopen
        if (response.status === 401) { window.location.href = 'login.php'; return; }

        const result = await response.json();

        if (response.ok) {
            document.getElementById('reis-' + id).remove(); // kaart uit DOM verwijderen
        } else {
            alert(result.message || 'Fout bij verwijderen.');
        }

    } catch (error) {
        console.error('Fout bij verwijderen:', error);
        alert('Kon reis niet verwijderen. Probeer opnieuw.');
    }
}

</script>

</body>
</html>
