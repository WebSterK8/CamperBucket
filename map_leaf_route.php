<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Afstand, reistijd en route.</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map { height: 500px; margin-top: 5px; border-radius: 5px; }
    #result { margin: 10px 0; font-weight: bold; }
</style>

</head>


<body>

<h2>Afstand, reistijd en route berekenen,</h2> <h2>tussen twee locaties naar keuze.</h2>

<p id="result">Klik op twee punten op de kaart</p>

<button type="button" class="btn btn-lg btn-outline-dark" 
data-bstoggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" 
title="Klik hier om opnieuw te beginnen."
onclick="resetKaart()">Reset
</button>

<div id="map"></div>


<script>
    // Kaart instellen
    const map = L.map('map').setView([51.05, 3.72], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
    }).addTo(map);

    // Variabelen
    let startPunt = null;
    let eindPunt = null;
    let startMarker = null;
    let eindMarker = null;
    let routeLayer = null;

    // Click-handler via Leaflet
    map.on('click', function(e) {

        // Eerste klik → startpunt
        if (!startPunt) {
            startPunt = e.latlng;
            startMarker = L.marker(startPunt).addTo(map);
            return;
        }

        // Tweede klik → eindpunt
        if (!eindPunt) {
            eindPunt = e.latlng;
            eindMarker = L.marker(eindPunt).addTo(map);

            // Bereken route
            berekenRoute();
        }

    });


    // Reset functie
    function resetKaart() {

        startPunt = eindPunt = null;
        
        if (startMarker) map.removeLayer(startMarker);
        if (eindMarker) map.removeLayer(eindMarker);
        if (routeLayer) map.removeLayer(routeLayer);

        startMarker = eindMarker = routeLayer = null;
        
        document.getElementById('result').innerText = "Klik twee punten op de kaart";

    }

    // Initialiseer tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bstoggle="tooltip"]');
    const tooltipList =
    [...tooltipTriggerList].map(tooltipTriggerEl => new
    bootstrap.Tooltip(tooltipTriggerEl));


    // route berekenen: Fetch naar PHP met try/catch
    async function berekenRoute() {

        try {
            const response = await fetch('API/bereken_route.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    lat1: startPunt.lat,
                    lng1: startPunt.lng,
                    lat2: eindPunt.lat,
                    lng2: eindPunt.lng
                })
            });
            
            if (!response.ok) throw new Error(`HTTP-error! Status: ${response.status}`);

            const data = await response.json();
            
            if (data.error) throw new Error(data.error);

            // Oude route verwijderen
             if (routeLayer) {
                map.removeLayer(routeLayer);
            }

            // Route tekenen
            routeLayer = L.geoJSON(data.geometry, { 
                style: { 
                    color: 'blue', 
                    weight: 5 
                }
            }).addTo(map);

            // Result tonen
            document.getElementById('result').innerHTML = `
            Afstand: ${data.afstand} km<br>
            Reistijd: ${data.tijd} min
            `;

            map.fitBounds(routeLayer.getBounds().pad(0.2));

        } catch (error) {
            console.error('Fout bij het berekenen van de route:', error);
            document.getElementById('result').innerText = "Er is een fout opgetreden bij het berekenen.";
        }
    }

</script>

</body>
</html>
