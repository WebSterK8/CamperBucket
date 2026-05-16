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



<!--main-->

<!-- Scrolling Sections -->

<section class="container-lg mt-2 fade-section">

 
 <?php include 'timelineSpreadsheet.php';?> 

</section>



<section class="container-lg mt-2 fade-section">

    
    <h1 class="m-5" style="text-align: center;">WishList</h1>

    <div class="row gx-3 gy-2">

        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 1</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 2</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 3</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>

    </div>

</section>



<section class="container-lg mt-2 fade-section">

    <div class="row gx-3 gy-2">

        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 4</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 5</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light border rounded h-100">
                <h3>Wish 6</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit...</p>
                <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris...</p>
            </div>
        </div>

    </div>

</section>

<?php include 'footer.php';?>




<!-- fade effect -->
<script>
    
gsap.utils.toArray(".fade-section").forEach((section, index,
sections) => {
    const nextSection = sections[index + 1];
    if (nextSection) {
        ScrollTrigger.create({
            trigger: section,
            start: "top top",
            end: "bottom bottom",
            scrub: true,
            onUpdate: (self) => {
                // Hoe ver we zijn
                const progress = self.progress;
                gsap.to(section, {
                    opacity: 1 - progress, // Vervagen
                    overwrite: true,
                    ease: "power1.out", // Geleidelijke overgang
                });
                gsap.to(nextSection, {
                    opacity: progress, // Verschijnen
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