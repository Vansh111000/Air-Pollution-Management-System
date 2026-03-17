<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar_station.php"); ?>

<main class="main">

<div class="section-header" style="margin-bottom: 2.5rem;">
    <h2 style="font-size: 1.75rem; font-weight: 700;">Station Dashboard</h2>
    <p style="color: var(--text-secondary);">Local metrics and sensor data.</p>
</div>

<div class="grid cards-grid">

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">AQI</div>
<div class="card-value text-red" style="font-size: 2.5rem; font-weight: 700;">142</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Temperature</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">29°C</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Humidity</div>
<div class="card-value text-blue" style="font-size: 2.5rem; font-weight: 700;">61%</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">PM2.5</div>
<div class="card-value text-yellow" style="font-size: 2.5rem; font-weight: 700;">82</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">CO</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">0.7</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">NO₂</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">34</div>
</div>

</div>

<div class="grid cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); margin-top: 2.5rem;">
    <div class="glass-card" style="border-radius: 12px; padding: 1.5rem;">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">AQI Trend</h3>
        </div>
        <canvas id="stationAqiChart"></canvas>
    </div>

    <div class="glass-card" style="border-radius: 12px; padding: 1.5rem;">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">Pollutant Distribution</h3>
        </div>
        <canvas id="pollutionPie"></canvas>
    </div>
</div>

</main>
<?php include("../includes/footer.php"); ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/apms/assets/js/mockdata.js"></script>
<script src="/apms/assets/js/alerts.js"></script>
<script src="/apms/assets/js/stationCharts.js"></script>