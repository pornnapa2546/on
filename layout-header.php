<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📦Orders</title>

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

/* ===== ORDER STYLE ===== */
.order {
  background: #fff;
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.status.pending { color: orange; }
.status.approved { color: green; }
.status.rejected { color: red; }

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  border: 1px solid #ddd;
  padding: 8px;
}
th {
  background: #f0f0f0;
  text-align: center;
}
td.num { text-align: right; }
td.center { text-align: center; }

button {
  margin-top: 10px;
  margin-right: 8px;
  padding: 6px 12px;
  cursor: pointer;
  border: none;
  border-radius: 4px;
}
.btn-approve { background:#28a745; color:#fff; }
.btn-reject  { background:#dc3545; color:#fff; }
.btn-receipt { background:#6c757d; color:#fff; }
</style>
</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="logo">Admin<span>Panel</span></div>

  <div class="nav-menu">
    <div class="nav-item" onclick="location.href='admin-dashboard.php'">
      <i class="fas fa-chart-pie"></i> Dashboard
    </div>

    <div class="nav-item active" onclick="location.href='admin.php'">
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

<div class="main-content">
