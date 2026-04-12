<!DOCTYPE html>
<html lang="nl">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initialscale=1.0">

<title>Galerij</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.8.1/css/lightgallery-bundle.min.css"/>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.8.1/css/lg-transitions.min.css"/>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="custom.css" rel="stylesheet">

<style>
#bootstrap-image-gallery img{
    height:250px;
    object-fit:cover;
    width:100%;
}
</style>

</head>


<body>

<div id="bootstrap-image-gallery">


 <div class="row gx-3 gy-3">

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG1L.jpg">
            <img src="Afbeeldingen/unsplashG1.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 1">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG2L.jpg">
            <img src="Afbeeldingen/unsplashG2.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 2">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG3L.jpg">
            <img src="Afbeeldingen/unsplashG3.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 3">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG4L.jpg">
            <img src="Afbeeldingen/unsplashG4.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 4">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG5L.jpg">
            <img src="Afbeeldingen/unsplashG5.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 5">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG6L.jpg">
            <img src="Afbeeldingen/unsplashG6.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 6">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG7L.jpg">
            <img src="Afbeeldingen/unsplashG7.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 7">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG8L.jpg">
            <img src="Afbeeldingen/unsplashG8.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 8">
        </a>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <a class="lg-item d-block" data-src="Afbeeldingen/unsplashG9L.jpg">
            <img src="Afbeeldingen/unsplashG9.jpg"
            class="img-fluid rounded shadow" alt="Afbeelding 9">
        </a>
    </div>

 </div>

</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script
src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.8.1/lightgallery.umd.min.js">
</script>

<script
src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.8.1/plugins/thumbnail/lg-thumbnail.umd.min.js">
</script>

<script
src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.8.1/plugins/zoom/lg-zoom.umd.min.js">
</script>


<script>

const container = document.querySelector('#bootstrap-image-gallery');

window.lightGallery(container, {

    selector: '.lg-item',

    mode: 'lg-fade', // Transition
    speed: 600,

    thumbnail: true, // Toon thumbnails
    animateThumb: true,

    zoom: true, // Zoom-functionaliteit inschakelen
    plugins: [ lgZoom, lgThumbnail],

});

</script>

</body>

</html>
