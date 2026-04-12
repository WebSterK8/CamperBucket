<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Timeline</title>

<link rel="stylesheet"
href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">



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

/* Hoofdtitel van de pagina */
h1 {
    text-align: center;
    color: #606f60;
}


</style>

</head>

<body>

<h1 class="mt-5" style="text-align: center;">Travelling Timeline</h1>

<div id="timeline-embed" style="width: 100%; height: 600px;"></div>

<script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>

<script>

const timelineData =
'https://docs.google.com/spreadsheets/d/e/2PACX-1vS9zYNr_XRLUr115gFjjcJl2dYgj3unlxcNODi_r1W8DYPRRPd0Ni6mfGdolSS5bXislxdd6Ed2bzcy/pubhtml';

// optie met Nederlandse datumnotatie
const options = {
    language: 'nl'
};

new TL.Timeline('timeline-embed', timelineData, options);

</script>



</body>

</html>