<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>

<nav class="navbar navbar-expand-lg mt-2 navbar-light bg-info rounded">

    <div class="container-fluid">
        
        <!-- Merknaam -->
        <a class="navbar-brand" href="index.php">
           
            <svg width="50" height="50" xmlns="http://www.w3.org/2000/svg">
                
                <!--C B-->
                <path d=" 
                M 20 12 
                C 6 12, 7 20, 15 21 
                L 30 21

                C 34 22, 34 27, 30 28 
                C 38 31, 38 39, 30 39
                L 12 39"

                fill="none" stroke="#7A8F7A" stroke-width="4" 
                stroke-linecap="round" stroke-linejoin="round"
                />
 
                <!-- Wieltjes 
                <circle cx="6" cy="42" r="2" fill="#7A8F7A" stroke="#7A8F7A" stroke-width="0.5"/>
                <circle cx="25" cy="42" r="2" fill="#7A8F7A" stroke="#7A8F7A" stroke-width="0.5"/>
                -->

                <!-- Groen afgerond vierkant -->
                <rect
                x="1.25"
                y="3.5"
                width="45"
                height="45"
                rx="11"
                ry="11"
                fill="none"
                stroke="#7A8F7A"
                stroke-width="2.75"
                />

            </svg>

        </a>

        <!-- Toggler voor responsief menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
            
        <!-- Menu-items -->
            <div class="collapse navbar-collapse" id="navbarContent">

            <ul class="navbar-nav mt-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <?php
                // Toon menu-items op basis van login status
                if (isset($_SESSION["ingelogd"]) && $_SESSION["ingelogd"] === true) {
                    // Gebruiker is ingelogd
                ?>

                <li class="nav-item">
                    <a class="nav-link" href="bucketlist.php">BucketList</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="checklist.php">CheckList</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="ontdek.php" id="navbarDropdown" data-bs-toggle="dropdown">Ontdek</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="locatie.php">Locaties</a></li>
                        <li><a class="dropdown-item" href="afbeeldingen.php">Afbeeldingen</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <span class="nav-link"><?php echo htmlspecialchars($_SESSION["Gebruikersnaam"]); ?></span>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>

                <?php } else { // Gebruiker is niet ingelogd ?>

                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>

                <?php } ?>


            </ul>

        </div>

    </div>

</nav>

<script>
// Actieve navigatielink markeren
document.querySelectorAll('.navbar .nav-link').forEach(function(link) {
    if (link.href === document.URL) {
        link.classList.add('active');
    }
});
</script>