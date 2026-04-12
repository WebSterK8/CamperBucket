<!DOCTYPE html>
<html lang="nl">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initialscale=1.0">

<title>swiper</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
<link href="custom.css" rel="stylesheet">

<style>

.swiper {
width: 100%;
height: auto;
border-radius: 5px;
position: relative; /* nodig voor absolute children (bolletjes) */

/* reserveer ruimte onder elke slide zodat de bolletjes eronder blijven */
padding-bottom: 3rem; /* pas aan voor gewenste afstand */
}

.swiper-slide {
display: flex;
justify-content: center;
align-items: center;

}

.swiper-slide img {
width: 100%;
height: auto;
border-radius: 5px;
}

/* Swiper Navigation Buttons (pijlen)*/
.swiper-button-next,
.swiper-button-prev {
color: var(--darksage);
border-radius: 5px;
padding: 8px;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
color: var(--beige);
background-color: var(--darksage);
}



/* Swiper Pagination Bullets (bolletjes) */
.swiper-pagination {
position:absolute;
left:0;
right:0;
/* plaats de paginering binnen de onderste padding in plaats van op de afbeelding */
bottom:1rem; /* afstand tot de onderkant van de container (incl. padding) */
}

.swiper-pagination-bullet {
background-color: var(--sagegreen);
width: 12px;    /* grotere bolletjes */
height: 12px;
margin: 0 4px;  /* ruimte tussen de bullets */
}

.swiper-pagination-bullet-active {
background-color: var(--darksage);
}



</style>


</head>


<body>

<!-- Swiper Container -->
<div class="swiper">

    <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="Afbeeldingen/camper1.jpg" alt="Slide 1"></div>
    
        <div class="swiper-slide"><img src="Afbeeldingen/camper2.jpg" alt="Slide 2"></div>
        
        <div class="swiper-slide"><img src="Afbeeldingen/unsplash1.jpg" alt="Slide 3"></div>
        
        <div class="swiper-slide"><img src="Afbeeldingen/unsplash2.jpg" alt="Slide 4"></div>
    </div>

    <!-- Navigatie -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

    <!-- Paginering -->
    <div class="swiper-pagination"></div>

</div>

<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<script>
const swiper = new Swiper('.swiper', {

    // Basisopties
    direction: 'horizontal',
    loop: true,
    effect: 'slide',

    // Swipe snelheid
    speed: 600,
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },

    // Laat de containerhoogte wisselen met de actieve slide
    autoHeight: true,

    // Automatische paginering (navigatiebolletjes)
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true,
    },

    // Navigatie
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    // Lazy loading
    lazy: {
        loadPrevNext: true,
    },
    
    });
</script>


</body>
</html>