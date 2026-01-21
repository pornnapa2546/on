<h2>📊 Dashboard</h2>

<div class="stats-cards">

  <div class="stat-card">
    <div class="card-value"><?= $totalOrders ?></div>
    <div class="card-label">Total Orders</div>
  </div>

  <div class="stat-card">
    <div class="card-value"><?= number_format($totalRevenue,2) ?> ฿</div>
    <div class="card-label">Total Revenue</div>
  </div>

  <div class="stat-card">
    <div class="card-value"><?= $pendingOrders ?></div>
    <div class="card-label">Pending Orders</div>
  </div>

</div>

<div class="table-card">
<h3>📦 Recent Orders</h3>

<table class="data-table">
<thead>
<tr>
  <th>ID</th>
  <th>Customer</th>
  <th>Total</th>
  <th>Status</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php while ($row = $recentOrders->fetch_assoc()): ?>
<tr>
  <td>#<?= $row['id'] ?></td>
  <td><?= htmlspecialchars($row['customer_name']) ?></td>
  <td><?= number_format($row['total'],2) ?> ฿</td>
  <td><?= strtoupper($row['status']) ?></td>
  <td>
    <a href="admin-dashboard.php?view=orders&id=<?= $row['id'] ?>"
       class="btn btn-outline">View</a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
