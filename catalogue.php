<?php include("includes/navbar.php"); ?>

<main class="main-container">

<div class="section-header" style="margin-top: 2rem;">
    <h2>Environmental Catalogue</h2>
    <p>Learn about air pollution, pollutants, and environmental impact.</p>
</div>

<div class="section-header" style="margin-top: 2.5rem; margin-bottom: 1rem;">
    <h3 style="font-size: 1.5rem;">Pollution News</h3>
</div>

<div class="grid cards-grid">

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">City AQI Rising</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
Recent data shows AQI levels increasing in industrial zones due to
high particulate matter emissions.
</p>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">Vehicle Emissions</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
Transportation contributes significantly to NO₂ and CO levels in
urban environments.
</p>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">Government Initiatives</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
New environmental regulations aim to reduce PM2.5 levels across
major cities.
</p>
</div>

</div>

<div class="section-header" style="margin-top: 3.5rem; margin-bottom: 1rem;">
    <h3 style="font-size: 1.5rem;">Pollutant Information</h3>
</div>

<div class="grid cards-grid">

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">PM2.5</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
Fine particulate matter smaller than 2.5 micrometers. It can
penetrate deep into lungs and cause respiratory problems.
</p>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">PM10</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
Coarser particulate matter that may irritate the respiratory system
and worsen asthma.
</p>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">Carbon Monoxide (CO)</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
A colorless gas produced by incomplete combustion of fuels. High
levels can reduce oxygen delivery in the body.
</p>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<h3 style="font-size: 1.15rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem;">Nitrogen Dioxide (NO₂)</h3>
<p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
Produced mainly by vehicles and power plants. It contributes to
smog and respiratory issues.
</p>
</div>

</div>

<div class="grid cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); margin-top: 2.5rem;">

    <div class="glass-card">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">Pollution Distribution</h3>
        </div>
        <canvas id="cataloguePie"></canvas>
    </div>

    <div class="glass-card">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem;">Weekly Pollution Levels</h3>
        </div>
        <canvas id="catalogueBar"></canvas>
    </div>

</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/apms/assets/js/catalogueCharts.js"></script>
<?php include("includes/footer.php"); ?>