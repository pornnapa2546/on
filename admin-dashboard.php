<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
require "config/db.php";


/* ======================
   DASHBOARD DATA
====================== */

// Total Orders
$totalOrders = $conn->query("
  SELECT COUNT(*) c FROM orders
")->fetch_assoc()['c'];


// Total Revenue (approved only)
$totalRevenue = $conn->query("
  SELECT IFNULL(SUM(total),0) AS revenue
  FROM orders
  WHERE status = 'approved'
")->fetch_assoc()['revenue'];

// Pending Orders
$pendingOrders = $conn->query("
  SELECT COUNT(*) c 
  FROM orders 
  WHERE status = 'pending'
")->fetch_assoc()['c'];


// Today Revenue
$todayRevenue = $conn->query("
  SELECT IFNULL(SUM(total),0) s
  FROM orders
  WHERE status = 'approved'
    AND DATE(created_at) = CURDATE()
")->fetch_assoc()['s'];



$todayOrders = $conn->query("
  SELECT COUNT(*) c
  FROM orders
  WHERE status = 'approved'
    AND DATE(created_at) = CURDATE()
")->fetch_assoc()['c'];


// Monthly Revenue
$monthlyRevenue = $conn->query("
  SELECT IFNULL(SUM(total),0) s
  FROM orders
  WHERE status = 'approved'
    AND YEAR(created_at) = YEAR(NOW())
    AND MONTH(created_at) = MONTH(NOW())
")->fetch_assoc()['s'];


$monthlyOrders = $conn->query("
  SELECT COUNT(*) c
  FROM orders
  WHERE status = 'approved'
    AND YEAR(created_at) = YEAR(NOW())
    AND MONTH(created_at) = MONTH(NOW())
")->fetch_assoc()['c'];


// Recent Orders
$recentOrders = $conn->query("
  SELECT * FROM orders
  ORDER BY id DESC
  LIMIT 5
");
$totalmenu = $conn->query("
  SELECT COUNT(*) c FROM menus
")->fetch_assoc()['c'];
$totaladmin = $conn->query("
  SELECT COUNT(*) c FROM admins
")->fetch_assoc()['c'];

$totaluser = $conn->query("
  SELECT COUNT(*) c FROM users
")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📊 Admin Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #f5f7fa;
}
.container {
  display: flex;
}
.sidebar {
  width: 240px;
  background: #1e293b;
  min-height: 100vh;
  color: #fff;
}
.sidebar .logo {
  padding: 20px;
  font-size: 22px;
}
.sidebar .logo span {
  color: #38bdf8;
}
.nav-menu {
  padding: 10px;
}
.nav-item {
  padding: 12px 15px;
  cursor: pointer;
  border-radius: 6px;
  margin-bottom: 8px;
}
.nav-item:hover,
.nav-item.active {
  background: #334155;
}
.nav-item i {
  margin-right: 10px;
}
.main-content {
  flex: 1;
  padding: 25px;
}
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
  gap: 20px;
  margin-bottom: 30px;
}
.stat-card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,.1);
}
.card-value {
  font-size: 28px;
  font-weight: 600;
}
.card-label {
  color: #555;
}
.table-card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,.1);
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table th,
.data-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
.data-table th {
  background: #f1f5f9;
}
.btn {
  padding: 6px 10px;
  border-radius: 4px;
  text-decoration: none;
  font-size: 14px;
}
.btn-outline {
  border: 1px solid #007bff;
  color: #007bff;
}
</style>
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="logo">Admin<span>Panel</span></div>

  <div class="nav-menu">
    <div class="nav-item active">
      <i class="fas fa-chart-pie"></i> Dashboard
    </div>

    <div class="nav-item" onclick="location.href='admin.php'">
      <i class="fas fa-shopping-cart"></i> Orders
    </div>
  <div class="nav-item" onclick="location.href='orders-report.php'">
      <i class="fas fa-chart-line"></i> Orders Report
    </div>
  <div class="nav-item" onclick="location.href='admin-menu.php'">
      <i class="fas fa-store"></i> Manage
    </div>
    <div class="nav-item" onclick="location.href='admin-register.php'">
      <i class="fas fa-user-plus"></i> Register Admin
    </div>
<div class="nav-item" onclick="location.href='admin-settings.php'">
  <i class="fas fa-cog"></i> Settings
</div>

    <div class="nav-item" onclick="location.href='logout.php'">
      <i class="fas fa-sign-out-alt"></i> Logout
    </div>
  </div>
</div>

<!-- MAIN -->
<div class="main-content">

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

  <div class="stat-card">
    <div class="card-value"><?= number_format($todayRevenue,2) ?> ฿</div>
    <div class="card-label">Today Revenue</div>
  </div>

  <div class="stat-card">
    <div class="card-value"><?= $todayOrders ?></div>
    <div class="card-label">Today Orders</div>
  </div>

<div class="stat-card">
  <div class="card-value"><?= number_format($monthlyRevenue, 2) ?> ฿</div>
  <div class="card-label">Monthly Revenue</div>
</div>

<div class="stat-card">
  <div class="card-value"><?= $monthlyOrders ?></div>
  <div class="card-label">Monthly Orders</div>
</div>
  <div class="stat-card">
    <div class="card-value"><?= $totalmenu ?></div>
    <div class="card-label">All Menus</div>
  </div>
<div class="stat-card">
    <div class="card-value"><?= $totaladmin ?></div>
    <div class="card-label">All admins</div>
  </div>
<div class="stat-card">
    <div class="card-value"><?= $totaluser ?></div>
    <div class="card-label">All Users</div>
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
  <td>#<?= $row['order_no'] ?></td>
  <td><?= htmlspecialchars($row['customer_name']) ?></td>
  <td><?= number_format($row['total'],2) ?> ฿</td>
  <td><strong><?= strtoupper($row['status']) ?></strong></td>
  <td>
    <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-outline">
  View
</a>

  </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>

</div>
</div>

</body>
</html>
