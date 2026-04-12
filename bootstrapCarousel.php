<!DOCTYPE html>
<html lang="nl">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Bootstrap Carousel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="custom.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>


<body>


<!-- bootstrap carousel -->

<div class="container-lg mt-5">
    
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">

        <!-- Indicators -->
        <div class="carousel-indicators">

            <button type="button" data-bs-target="#carouselExample"
            data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide1">
            </button>

            <button type="button" data-bs-target="#carouselExample"
            data-bs-slide-to="1" aria-label="Slide 2">
            </button>

            <button type="button" data-bs-target="#carouselExample"
            data-bs-slide-to="2" aria-label="Slide 3">
            </button>

            <button type="button" data-bs-target="#carouselExample"
            data-bs-slide-to="3" aria-label="Slide 4">
            </button>

            <button type="button" data-bs-target="#carouselExample"
            data-bs-slide-to="4" aria-label="Slide 5">
            </button>
            
        </div>


        <!-- Slides -->
        <div class="carousel-inner">

            <div class="carousel-item active">

                <img src="Afbeeldingen/camper1.jpg"
                class="d-block w-100" alt="Slide 1">

                <div class="carousel-caption">
                    <h5>België</h5>
                    <p>Camper plekje in de ochtend.</p>
                </div>

            </div>

            
            <div class="carousel-item">

                <img src="Afbeeldingen/camper2.jpg"
                class="d-block w-100" alt="Slide 2">
                
                <div class="carousel-caption">
                    <h5>België</h5>
                    <p>Camper plekje in de avond.</p>
                </div>

            </div>

            
            <div class="carousel-item">

                <img src="Afbeeldingen/tekening-zee.jpg"
                class="d-block w-100 contain" loading="lazy" alt="Slide 3">
                
                <div class="carousel-caption">
                    <h5>België</h5>
                    <p>Met de camper aan zee.</p>
                </div>

            </div>


            <div class="carousel-item">

                <img src="Afbeeldingen/tekening-droom.jpg"
                class="d-block w-100 contain" loading="lazy" alt="Slide 4">
                
                <div class="carousel-caption">
                    <h5>Noorwegen</h5>
                    <p>Van droom naar bestemming.</p>
                </div>

            </div>

            

        </div>

        <!-- Navigatieknoppen -->
                <button class="carousel-control-prev" type="button" 
                    data-bs-target="#carouselExample" data-bs-slide="prev">
        
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Vorige</span> 
                </button>

                <button class="carousel-control-next" type="button" 
                    data-bs-target="#carouselExample" data-bs-slide="next">

                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Volgende</span>
                </button>

    </div>

</div>


</body>

</html>

