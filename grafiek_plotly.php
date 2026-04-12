


<h2 class="text-darksage">Temperaturen per hoofdstad</h2>


<div id="citySelectors" class="my-4"> 
  <!-- checkboxes worden via JS gevuld -->
</div>


<p class="small text-muted">Tip: Klik op de knoppen om de lijnen van een stad uit of aan te zetten.</p>
<p class="small text-muted"> Je kunt ook dubbelklikken op een lijn of een regel in de legende van de grafiek om alleen die lijn te tonen. Dubbelklik opnieuw om alle lijnen weer zichtbaar te maken.</p>

<div id="tempChart"></div>


<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>


<script>


const months = ["Jan","Feb","Mrt","Apr","Mei","Jun","Jul","Aug","Sep","Okt","Nov","Dec"];

const cityColors = {
  "Brussel": "#1f77b4",   // blauw
  "Amsterdam": "#ff7f0e", // oranje
  "Parijs": "#2ca02c",    // groen
  "Berlijn": "#d62728",   // rood
  "Boedapest": "#9467bd", // paars
  "Oslo": "#8c564b"       // bruin
};

// GEMIDDELDE MAX DAGTEMPERATUUR (°C)
const maxData = {
  "Brussel":  [6,7,11,15,19,22,24,24,20,15,10,7],
  "Amsterdam":[6,7,10,14,18,21,23,23,19,14,9,6],
  "Parijs":   [7,9,13,16,20,24,26,26,22,17,11,8],
  "Berlijn":  [3,5,10,15,19,23,25,25,20,14,8,4],
  "Boedapest":[3,6,12,18,23,27,30,30,24,17,9,4],
  "Oslo":     [0,1,6,11,17,21,23,22,17,11,5,1]
};

// GEMIDDELDE MIN DAGTEMPERATUUR (°C)
const minData = {
  "Brussel":  [1,1,3,6,10,13,15,15,12,8,4,2],
  "Amsterdam":[1,1,3,6,9,12,14,14,11,7,4,2],
  "Parijs":   [2,3,5,8,11,14,16,16,13,9,5,3],
  "Berlijn":  [-2,-1,2,5,9,13,15,14,10,6,2,-1],
  "Boedapest":[-1,1,5,9,14,17,19,19,14,9,4,0],
  "Oslo":     [-7,-7,-3,2,7,11,13,12,8,4,-1,-5]
};

// lijst van beschikbare steden (keys uit maxData)
const cities = Object.keys(maxData);

// layout definiëren 
var layout = {
  autosize: true,
  title: "Gemiddelde maandelijkse min / max dagtemperatuur",
  yaxis: { title: "Temperatuur (°C)" },
  hovermode: false,    // hoverlabels uitschakelen
  margin: { b: 80, l: 60, r: 40, t: 80 },  // ruimte voor legende onder grafiek
  legend: {
    x: 0.5,
    y: -0.3,
    xanchor: 'center',
    yanchor: 'top',
    orientation: 'h'   
  },
  shapes: [
    {
      type: 'rect',
      xref: 'paper',
      yref: 'y',
      x0: 0,
      x1: 1,
      y0: 19,
      y1: 26,
      fillcolor: 'rgba(0,200,0,0.2)',
      line: { width: 0 }
    }
  ]
};

function renderTempChart(selectedCities) {
  let traces = [];
  selectedCities.forEach(city => {
    traces.push({
      x: months,
      y: maxData[city],
      mode: 'lines',
      name: city + " Max",
      line: { width: 3, color: cityColors[city] },
      hoverinfo: 'none'
    });
    traces.push({
      x: months,
      y: minData[city],
      mode: 'lines',
      name: city + " Min",
      line: { dash: 'dot', color: cityColors[city] },
      hoverinfo: 'none'
    });
  });
  

  Plotly.newPlot('tempChart', traces, layout, {responsive: true});
  
  // resize event listener bij laden
  window.addEventListener('load', () => {
    Plotly.Plots.resize('tempChart');
  });
}


function updateChart() {
  const chosen = Array.from(document.querySelectorAll('#citySelectors input:checked'))
                       .map(i => i.value);
  renderTempChart(chosen);
}



function createSelectors() {
  const container = document.getElementById('citySelectors');
  container.classList.add("d-flex", "flex-wrap", "gap-2");

  cities.forEach((city, index) => {
    const cb = document.createElement('input');
    cb.type = "checkbox";
    cb.className = "btn-check";
    cb.id = "city" + index;
    cb.value = city;
    cb.checked = true; // standaard aan

    const label = document.createElement('label');
    label.className = "btn btn-outline-dark rounded-pill active"; // standaard actief
    label.htmlFor = cb.id;
    label.textContent = city;

    // klik-event voor toggle
    cb.addEventListener('change', () => {
      label.classList.toggle('active'); // active class togglen
      updateChart();                    // grafiek updaten
    });

    container.appendChild(cb);
    container.appendChild(label);
  });
}



// initialisatie
createSelectors();
renderTempChart(cities);

// resize functie
//window.addEventListener('resize', function() {
  //Plotly.Plots.resize(document.getElementById('tempChart'));
//});

</script>

