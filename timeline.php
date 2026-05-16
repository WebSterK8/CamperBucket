<link rel="stylesheet" href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">

<style>

.tl-slide-content {
    color: #606f60 !important;
}

.tl-text {
    color: #606f60 !important;
}

/* tekst onder headline */
.tl-text-content {
    color: #606f60 !important;
}

/* titel van het event */
.tl-headline {
    color: #606f60 !important;
}

/* datum bovenaan een event */
.tl-headline-date {
    color: #606f60 !important;
}

/* tekst onder afbeeldingen */
.tl-caption {
    color: #606f60 !important;
}

/* datums op de tijdlijn */
.tl-timeaxis .tl-timeaxis-tick-text {
    color: #606f60 !important;
}

</style>

<h1 class="mt-5" style="text-align: center;">Travelling Timeline</h1>

<div id="timeline-embed" style="width: 100%; height: 600px;"></div>

<script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>

<script>

// timeline data ophalen uit database via Fetch API
fetch('API/get_timeline.php')
    .then(response => {
        // tweede verdedigingslinie: sessie verlopen
        if (response.status === 401) { window.location.href = 'login.php'; return; }
        return response.json();
    })
    .then(data => {
        new TL.Timeline('timeline-embed', data, { language: 'nl' }); // optie met Nederlandse datumnotatie
    })
    .catch(error => {
        console.error('Fout bij laden timeline:', error);
    });

</script>
