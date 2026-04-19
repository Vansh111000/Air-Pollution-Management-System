<?php
session_start();

if($_SESSION['role'] != "admin"){
header("Location: ../login.php");
}
?>
<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar_admin.php"); ?>

<main class="main">

<div class="section-header" style="margin-bottom: 2.5rem;">
    <h2 style="font-size: 1.75rem; font-weight: 700;">Admin Dashboard</h2>
    <p style="color: var(--text-secondary);">System overview and statistics.</p>
</div>

<div class="grid cards-grid">

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Monitoring Stations</div>
<div class="card-value text-blue" style="font-size: 2.5rem; font-weight: 700;">12</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Active Sensors</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">86</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Offline Sensors</div>
<div class="card-value text-red" style="font-size: 2.5rem; font-weight: 700;">4</div>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem; text-align: center;">
<div class="card-title">Average AQI Today</div>
<div class="card-value text-yellow" style="font-size: 2.5rem; font-weight: 700;">142</div>
</div>

</div>

<div class="glass-card" style="margin-top: 2.5rem; border-radius: 12px; padding: 1.5rem;">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.25rem;">AQI Trend</h3>
    </div>
    <canvas id="aqiChart"></canvas>
</div>

</main>
<?php include("../includes/footer.php"); ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/apms/assets/js/mockdata.js"></script>
<script src="/apms/assets/js/alerts.js"></script>
<script src="/apms/assets/js/charts.js"></script>