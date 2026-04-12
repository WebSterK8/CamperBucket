

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />



<h2 class="text-darksage">Campings zoeken</h2>

<div id="info" class="small text-muted my-4" >
  <p> Stap 1: klik op de kaart.</p>
</div>

<button id="searchBtn" class="mb-4 btn btn-outline-dark rounded-pill" disabled>Zoek campings in de buurt (10km)</button>




<div id="map"></div>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

  // =========================
  // Hulpjes 
  // =========================

  // div info 
  function setInfo(html) {
    document.getElementById("info").innerHTML = html;
  }

  // Afstand mooi tonen
  function formatDistance(meters) {
    if (meters >= 1000) return (meters / 1000).toFixed(1) + " km";
    return Math.round(meters) + " m";
  }

  // Tijd mooi tonen
  function formatTime(seconds) {
    const s = Math.round(seconds);
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    if (h > 0) return h + "u " + m + "m";
    return m + "m";
  }


  // =========================
  // Fetch
  // =========================

  async function fetchData(url) {
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error("Netwerkresponse was niet ok (" + response.status + " " + response.statusText + ")");
      }
      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Er was een probleem met het ophalen van de gegevens:", error);
      throw error;
    }
  }



  // =========================
  // Kaart instellen (Leaflet)
  // =========================

  const map = L.map("map").setView([51.05, 3.73], 9); // België

  // Tiles = OpenStreetMap (simpel, geen key nodig in frontend)
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "© OpenStreetMap"
  }).addTo(map);


  // =========================
  // Variabelen
  // =========================

  let startPoint = null;          // {lat, lon}
  let startMarker = null;         // Leaflet marker
  let campingMarkers = [];        // array van markers
  let routeLine = null;           // polyline

  const searchBtn = document.getElementById("searchBtn");



  // =========================
  // Stap 1: klik op kaart = startpunt
  // =========================

  map.on("click", function(e) {
    startPoint = { lat: e.latlng.lat, lon: e.latlng.lng };

    // oude marker weg?
    if (startMarker) map.removeLayer(startMarker);

    // nieuwe marker
    startMarker = L.marker([startPoint.lat, startPoint.lon]).addTo(map)
      .bindPopup("Startpunt").openPopup();

    // route weg (als die er was)
    if (routeLine) {
      map.removeLayer(routeLine);
      routeLine = null;
    }

    setInfo(
      "<b>Startpunt gekozen!</b><br>" +
      "Stap 2: klik op <b>Zoek campings</b>."
    );

    // knop activeerbaar
    searchBtn.disabled = false;
  });


  // =========================
  // Stap 2: campings zoeken rond startpunt
  // =========================


  async function searchCampings() {
    if (!startPoint) {
      alert("Klik eerst op de kaart voor een startpunt.");
      return;
    }

    setInfo("Campings zoeken binnen 10 km...");
    searchBtn.disabled = true;

    // oude camping markers weg
    for (let i = 0; i < campingMarkers.length; i++) {
      map.removeLayer(campingMarkers[i]);
    }
    campingMarkers = [];

    // URL naar PHP
    const url =
      "API/berekenPlaces.php?action=campings" +
      "&lat=" + encodeURIComponent(startPoint.lat) +
      "&lon=" + encodeURIComponent(startPoint.lon) +
      "&radius=10000&limit=50";

    const data = await fetchData(url);
    const features = data.features || [];

    // markers zetten
    for (let i = 0; i < features.length; i++) {
      const p = features[i].properties;
      const name = p.name || "Camping";

      const marker = L.marker([p.lat, p.lon]).addTo(map);
      marker.bindPopup("<b>" + name + "</b><br>" + (p.formatted || "") + "<br><i>Klik om route te tonen</i>");

      // Stap 3: selecteer camping = klik marker -> route
      marker.on("click", function() {
        showRouteToCamping(p.lat, p.lon, name);
      });

      campingMarkers.push(marker);
    }

    setInfo(
      "<b>Campings gevonden:</b> " + features.length + "<br>" +
      "Stap 3: klik op een camping-marker.<br>" +
      "Stap 4: dan tonen we de route."
    );

    searchBtn.disabled = false;
  }

  // =========================
  // Stap 4: route tonen naar gekozen camping
  // =========================
  
  async function showRouteToCamping(destLat, destLon, destName) {
    if (!startPoint) return;

    setInfo("Route berekenen naar " + destName + "...");

    // oude route weg
    if (routeLine) {
      map.removeLayer(routeLine);
      routeLine = null;
    }

    // URL naar PHP
    const url =
      "API/berekenPlaces.php?action=route" +
      "&fromLat=" + encodeURIComponent(startPoint.lat) +
      "&fromLon=" + encodeURIComponent(startPoint.lon) +
      "&toLat=" + encodeURIComponent(destLat) +
      "&toLon=" + encodeURIComponent(destLon) +
      "&mode=drive";

    const data = await fetchData(url);
    const feature = (data.features || [])[0];

    if (!feature) {
      setInfo("Geen route gevonden.");
      return;
    }

    const distance = feature.properties.distance;
    const time = feature.properties.time;

    // route tekenen
    const coords = feature.geometry.coordinates[0]; // [ [lon,lat], ... ]
    const latLngs = coords.map(function(pt) { return [pt[1], pt[0]]; });

    routeLine = L.polyline(latLngs).addTo(map);
    map.fitBounds(routeLine.getBounds(), { padding: [20, 20] });

    setInfo(
      "<b>Route naar:</b> " + destName + "<br>" +
      "📏 Afstand: <b>" + formatDistance(distance) + "</b><br>" +
      "⏱ Tijd: <b>" + formatTime(time) + "</b>"
    );
  }

  // knop event
  searchBtn.addEventListener("click", function() {
      searchCampings().catch(function(err) {
        console.error(err);
        alert(err.message || String(err));
        setInfo("Fout bij campings zoeken.");
        searchBtn.disabled = false;
      });
    });

  </script>


