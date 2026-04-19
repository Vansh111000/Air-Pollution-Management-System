<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar_admin.php"); ?>

<main class="main">

<div class="section-header" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.75rem; font-weight: 700;">Pollution Alerts</h2>
    <p style="color: var(--text-secondary);">Real-time hazard notifications.</p>
</div>

<div class="glass-card" style="padding: 0; overflow: auto; border-radius: 12px;">
<table class="table" style="margin: 0;">
<thead>
<tr>
<th>ID</th>
<th>Station</th>
<th>Pollutant</th>
<th>Value</th>
<th>Alert Level</th>
<th>Timestamp</th>
</tr>
</thead>

<tbody>

<tr>
<td>1</td>
<td>Industrial Area Station</td>
<td>AQI</td>
<td style="font-weight: bold;">245</td>
<td><span style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Red Alert</span></td>
<td>10:45 AM</td>
</tr>

<tr>
<td>2</td>
<td>Central City Station</td>
<td>PM2.5</td>
<td style="font-weight: bold;">165</td>
<td><span style="background: rgba(250, 204, 21, 0.1); color: var(--warning); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Yellow Alert</span></td>
<td>09:30 AM</td>
</tr>

<tr>
<td>3</td>
<td>North Monitoring Point</td>
<td>CO</td>
<td style="font-weight: bold;">9.2</td>
<td><span style="background: rgba(250, 204, 21, 0.1); color: var(--warning); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Yellow Alert</span></td>
<td>08:10 AM</td>
</tr>

<tr>
<td>4</td>
<td>Industrial Area Station</td>
<td>NO2</td>
<td style="font-weight: bold;">180</td>
<td><span style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Red Alert</span></td>
<td>07:50 AM</td>
</tr>

</tbody>
</table>
</div>

</main>
<?php include("../includes/footer.php"); ?>