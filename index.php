<?php require_once 'dbconnect.php'; ?>
<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Welkom</title>

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





<!--main-->

<!-- Scrolling Sections -->

<section class="container-lg mt-2 fade-section">
<?php include 'swiper.php'?>
</section>


<div id="content">

 <section class="container-lg mt-2 fade-section">

    <div class="row gx-3 gy-2">

        <div class="col-12 col-md-6 jumbotron-col">

            <!-- Jumbo Tron -->
            <div class="speech-container p-5 bg-info rounded-3 h-100">

                <div class="text-to-speech"> 

                    <h3 class="display-6 mb-3">Welkom</h3>
                
                    <p>In deze app kunnen we al onze ideeën bundelen.</p> 
                    <p>Van eendags-trips tot weekends en iets langere reizen.</p>

                </div>

                <button type="button" class="speech-btn" aria-label="Lees de tekst Welkom voor">Voorlezen</button>
  
            </div>
            
        </div>

        
        <div class="col-12 col-md-6 jumbotron-col">

            <!-- Jumbo Tron -->
            <div class="speech-container p-5 bg-info rounded-3 h-100">

                <div class="text-to-speech">

                    <h3 class="display-6 mb-3">Ultieme reis</h3>

                    <p>Ons doel is een reis naar Noorwegen.</p>
                    <p>Eerst nog wat oefenen met kleinere uitstapjes.</p>
                    <p>Ontdek op de kaart hieronder afstanden, reistijden en routes.</p>

                </div> 

                <button type="button" class="speech-btn" aria-label="Lees de tekst Welkom voor">Voorlezen</button>

            </div>

        </div>

    </div>

 </section>

</div>


<div class="container-lg mt-2 fade-section">

    <section class="mt-5 p-3 bg-alfasage text-darksage text-center rounded">
        
        <?php include 'map_leaf_route.php'; ?>
    
</section>

</div>


<!--footer-->

<?php include 'footer_licentie.php';?>




<script>

// Selecteer alle knoppen met class "speech-btn"
document.querySelectorAll('.speech-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Zoek de container van de knop
        let container = button.closest('.speech-container');
        let textDiv = container.querySelector('.text-to-speech');
        let msg = textDiv.innerText;

        // Maak en speel de spraak af
        let speech = new SpeechSynthesisUtterance();
        speech.lang = "nl-NL";
        speech.text = msg;
        speech.volume = 1;
        speech.rate = 1;
        speech.pitch = 1;

        window.speechSynthesis.speak(speech);
    });
});

</script>


<!--<script>

document.getElementById("downloadPDF").addEventListener("click", function()
{
const element = document.getElementById('content');

const options = {
    filename: 'CamperBucket_Welkom.pdf', // Instellen naam van de PDF
    margin: 1, // Marges in inches (1 inch = ~2.54 cm)
    image: { type: 'jpeg', quality: 0.95 }, // Beeldkwaliteit 95% met JPEG
    html2canvas: { scale: 2, useCORS: true }, // Hoge schaal (2x) en CORS inschakelen
    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' } // Staand A4-formaat
}; 

html2pdf().set(options).from(element).save();
});

</script>-->



<!-- fade effect -->
<script>
gsap.registerPlugin(ScrollTrigger);

// Op mobiel elke jumbotron-kolom apart animeren zodat de bovenste eerst verdwijnt
const isMobile = window.innerWidth < 768;
let fadeSections;

if (isMobile) {
    fadeSections = [];
    document.querySelectorAll('.fade-section').forEach(el => {
        const cols = el.querySelectorAll('.jumbotron-col');
        if (cols.length > 0) {
            cols.forEach(col => fadeSections.push(col));
        } else {
            fadeSections.push(el);
        }
    });
} else {
    fadeSections = gsap.utils.toArray('.fade-section');
}

fadeSections.forEach((section, index) => {
    const nextSection = fadeSections[index + 1];
    if (nextSection) {
        ScrollTrigger.create({
            trigger: section,
            start: "top top",
            end: "bottom bottom",
            scrub: true,
            onUpdate: (self) => {
                const progress = self.progress;
                gsap.to(section, {
                    opacity: 1 - progress,
                    overwrite: true,
                    ease: "power1.out",
                });
                gsap.to(nextSection, {
                    opacity: progress,
                    overwrite: true,
                    ease: "power1.in",
                });
            },
        });
    }
});
</script>

</body>


</html>