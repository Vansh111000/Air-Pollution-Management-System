<?php include("includes/navbar.php"); ?>

<main class="main-container">

<div class="section-header" style="margin-top: 2rem;">
    <h2>Public Air Quality Dashboard</h2>
    <p>Detailed view of air quality metrics and pollution composition.</p>
</div>

<div class="grid cards-grid">

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">AQI</div>
<div class="card-value text-red" style="font-size: 2.5rem; font-weight: 700;">142</div>
</div>

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Temperature</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">29°C</div>
</div>

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">PM2.5</div>
<div class="card-value text-yellow" style="font-size: 2.5rem; font-weight: 700;">82</div>
</div>

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Humidity</div>
<div class="card-value text-blue" style="font-size: 2.5rem; font-weight: 700;">61%</div>
</div>

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">CO</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">0.7</div>
</div>

<div class="card glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">NO₂</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">34</div>
</div>

</div>

<div class="grid cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); margin-top: 2.5rem;">
    <div class="glass-card">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">Weekly AQI Trend</h3>
        </div>
        <canvas id="publicAqiChart"></canvas>
    </div>

    <div class="glass-card">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">Pollution Composition</h3>
        </div>
        <canvas id="publicPollutionPie"></canvas>
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/apms/assets/js/publicCharts.js"></script>
<?php include("includes/footer.php"); ?>