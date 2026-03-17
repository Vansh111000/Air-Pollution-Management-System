<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar_admin.php"); ?>

<main class="main">

<div class="section-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 700;">Monitoring Stations</h2>
        <p style="color: var(--text-secondary);">Manage all registered stations across zones.</p>
    </div>
    <button class="btn btn-primary" style="padding: 0.6rem 1.2rem;">+ Add Station</button>
</div>

<div class="glass-card" style="padding: 0; overflow: auto; border-radius: 12px;">
<table class="table" style="margin: 0;">
<thead>
<tr>
<th>ID</th>
<th>Station Name</th>
<th>Location</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<tr>
<td>1</td>
<td>Central City Station</td>
<td>Downtown</td>
<td><span style="color: var(--success); font-weight: 500;">Active</span></td>
<td>
<button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-right: 0.5rem;">Edit</button>
<button class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.4rem 0.8rem; font-size: 0.85rem;">Delete</button>
</td>
</tr>

<tr>
<td>2</td>
<td>North Monitoring Point</td>
<td>North District</td>
<td><span style="color: var(--success); font-weight: 500;">Active</span></td>
<td>
<button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-right: 0.5rem;">Edit</button>
<button class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.4rem 0.8rem; font-size: 0.85rem;">Delete</button>
</td>
</tr>

<tr>
<td>3</td>
<td>Industrial Area Station</td>
<td>Industrial Zone</td>
<td><span style="color: var(--warning); font-weight: 500;">Warning</span></td>
<td>
<button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-right: 0.5rem;">Edit</button>
<button class="btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.4rem 0.8rem; font-size: 0.85rem;">Delete</button>
</td>
</tr>

</tbody>
</table>
</div>

</main>
<?php include("../includes/footer.php"); ?>