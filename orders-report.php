<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
require "config/db.php";

/* =========================
   VIEW MODE
========================= */
$view = $_GET['view'] ?? 'day';

/* =========================
   PREPARE QUERY
========================= */
if ($view === 'day') {
  $date = $_GET['date'] ?? date('Y-m-d');
  $stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE DATE(created_at) = ?
    ORDER BY created_at DESC
  ");
  $stmt->bind_param("s", $date);

} elseif ($view === 'month') {
  $month = $_GET['month'] ?? date('m');
  $year  = $_GET['year'] ?? date('Y');
  $stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE MONTH(created_at) = ?
      AND YEAR(created_at) = ?
    ORDER BY created_at DESC
  ");
  $stmt->bind_param("ii", $month, $year);

} else { // year
  $year = $_GET['year'] ?? date('Y');
  $stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE YEAR(created_at) = ?
    ORDER BY created_at DESC
  ");
  $stmt->bind_param("i", $year);
}

$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📊 Orders Report</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{margin:0;font-family:'Poppins',sans-serif;background:#f5f7fa}
.container{display:flex}
.sidebar{width:240px;background:#1e293b;min-height:100vh;color:#fff}
.sidebar .logo{padding:20px;font-size:22px}
.sidebar .logo span{color:#38bdf8}
.nav-menu{padding:10px}
.nav-item{padding:12px 15px;cursor:pointer;border-radius:6px;margin-bottom:8px}
.nav-item:hover,.nav-item.active{background:#334155}
.nav-item i{margin-right:10px}
.main-content{flex:1;padding:25px}

.tabs{
  display:flex;
  gap:10px;
  margin-bottom:20px;
}
.tab{
  padding:8px 14px;
  border-radius:8px;
  text-decoration:none;
  background:#e5e7eb;
  color:#000;
  font-size:14px;
}
.tab.active{
  background:#38bdf8;
  color:#fff;
}

.card{
  background:#fff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.data-table{width:100%;border-collapse:collapse}
.data-table th,.data-table td{padding:10px;border-bottom:1px solid #ddd}
.data-table th{background:#f1f5f9}

.btn{
  padding:6px 10px;
  border-radius:4px;
  text-decoration:none;
  font-size:14px;
}
.btn-outline{
  border:1px solid #007bff;
  color:#007bff;
}
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
    <div class="nav-item" onclick="location.href='admin.php'">
      <i class="fas fa-shopping-cart"></i> Orders
    </div>
    <div class="nav-item active">
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

<h2>📊 Orders Report</h2>

<!-- TABS -->
<div class="tabs">
  <a class="tab <?= $view==='day'?'active':'' ?>" href="?view=day">รายวัน</a>
  <a class="tab <?= $view==='month'?'active':'' ?>" href="?view=month">รายเดือน</a>
  <a class="tab <?= $view==='year'?'active':'' ?>" href="?view=year">รายปี</a>
</div>

<!-- FILTER -->
<form method="get" style="margin-bottom:20px;">
<input type="hidden" name="view" value="<?= $view ?>">

<?php if ($view === 'day'): ?>
  <input type="date" name="date" value="<?= $date ?>">

<?php elseif ($view === 'month'): ?>
  <select name="month">
    <?php for($m=1;$m<=12;$m++): ?>
      <option value="<?= $m ?>" <?= $month==$m?'selected':'' ?>>
        <?= date('F', mktime(0,0,0,$m,1)) ?>
      </option>
    <?php endfor; ?>
  </select>
  <select name="year">
    <?php for($y=date('Y');$y>=date('Y')-5;$y--): ?>
      <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option>
    <?php endfor; ?>
  </select>

<?php else: ?>
  <select name="year">
    <?php for($y=date('Y');$y>=date('Y')-10;$y--): ?>
      <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option>
    <?php endfor; ?>
  </select>
<?php endif; ?>

<button class="btn btn-outline">ดูออเดอร์</button>
</form>

<!-- TABLE -->
<div class="card">
<table class="data-table">
<thead>
<tr>
  <th>Order No</th>
  <th>Customer</th>
  <th>Total</th>
  <th>Status</th>
  <th>Date</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php if ($orders->num_rows): ?>
  <?php while($o=$orders->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($o['order_no']) ?></td>
    <td><?= htmlspecialchars($o['customer_name']) ?></td>
    <td><?= number_format($o['total'],2) ?> ฿</td>
    <td><strong><?= strtoupper($o['status']) ?></strong></td>
    <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
    <td>
      <a href="order-detail.php?id=<?= $o['id'] ?>" class="btn btn-outline">
        View
      </a>
    </td>
  </tr>
  <?php endwhile; ?>
<?php else: ?>
<tr>
  <td colspan="6" style="text-align:center;color:#999;">
    ไม่มีออเดอร์ในช่วงที่เลือก
  </td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</div>
</body>
</html>
