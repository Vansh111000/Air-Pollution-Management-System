<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar_station.php"); ?>

<main class="main">

<div class="section-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 700;">Sensor Management</h2>
        <p style="color: var(--text-secondary);">Manage and monitor individual sensors.</p>
    </div>
    <button class="btn btn-primary" style="padding: 0.6rem 1.2rem;">+ Add Sensor</button>
</div>

<div class="grid cards-grid">

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<div class="card-title">PM2.5 Sensor</div>
<div class="card-value text-yellow" style="font-size: 2.5rem; font-weight: 700;">82</div>
<p style="color: var(--success); font-weight: 500; font-size: 0.9rem; margin-top: 0.5rem;">Status: Active</p>
<p style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 1rem;">Last Update: 2 mins ago</p>
<button class="btn btn-outline" style="width: 100%; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">Remove</button>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<div class="card-title">Temperature Sensor</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">29°C</div>
<p style="color: var(--success); font-weight: 500; font-size: 0.9rem; margin-top: 0.5rem;">Status: Active</p>
<p style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 1rem;">Last Update: 1 min ago</p>
<button class="btn btn-outline" style="width: 100%; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">Remove</button>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<div class="card-title">Humidity Sensor</div>
<div class="card-value text-blue" style="font-size: 2.5rem; font-weight: 700;">61%</div>
<p style="color: var(--success); font-weight: 500; font-size: 0.9rem; margin-top: 0.5rem;">Status: Active</p>
<p style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 1rem;">Last Update: 3 mins ago</p>
<button class="btn btn-outline" style="width: 100%; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">Remove</button>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<div class="card-title">CO Sensor</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">0.7</div>
<p style="color: var(--warning); font-weight: 500; font-size: 0.9rem; margin-top: 0.5rem;">Status: Warning</p>
<p style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 1rem;">Last Update: 2 mins ago</p>
<button class="btn btn-outline" style="width: 100%; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">Remove</button>
</div>

<div class="glass-card hover-lift" style="padding: 1.5rem;">
<div class="card-title">NO₂ Sensor</div>
<div class="card-value" style="font-size: 2.5rem; font-weight: 700;">34</div>
<p style="color: var(--success); font-weight: 500; font-size: 0.9rem; margin-top: 0.5rem;">Status: Active</p>
<p style="color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 1rem;">Last Update: 2 mins ago</p>
<button class="btn btn-outline" style="width: 100%; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">Remove</button>
</div>

</div>

</main>
<?php include("../includes/footer.php"); ?>