<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
require "config/db.php";

/* ======================
   FETCH ADMIN
====================== */
$admin_username = $_SESSION['admin'];

$stmt = $conn->prepare("
  SELECT username
  FROM admins
  WHERE username = ?
");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

/* ======================
   LOAD SHOP SETTINGS
====================== */
$settings = [];
$r = $conn->query("SELECT * FROM settings");
while ($row = $r->fetch_assoc()) {
  $settings[$row['name']] = $row['value'];
}

$shop_mode  = $settings['shop_manual_status'] ?? 'auto';
$openTime   = $settings['shop_open_time'] ?? '08:00';
$closeTime  = $settings['shop_close_time'] ?? '18:00';

/* ======================
   UPDATE PROFILE + SHOP SETTINGS
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  /* ---- update admin profile ---- */
  $new_username = $_POST['username'];
  $new_password = $_POST['password'];

  if (!empty($new_password)) {
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("
      UPDATE admins
      SET username = ?, password = ?
      WHERE username = ?
    ");
    $stmt->bind_param("sss", $new_username, $hash, $admin_username);
  } else {
    $stmt = $conn->prepare("
      UPDATE admins
      SET username = ?
      WHERE username = ?
    ");
    $stmt->bind_param("ss", $new_username, $admin_username);
  }
  $stmt->execute();
  $_SESSION['admin'] = $new_username;

  /* ---- update shop settings ---- */
  $map = [
    'shop_manual_status' => $_POST['shop_mode'],
    'shop_open_time'     => $_POST['shop_open_time'],
    'shop_close_time'    => $_POST['shop_close_time']
  ];

  foreach ($map as $k => $v) {
    $stmt = $conn->prepare("
      UPDATE settings SET value=? WHERE name=?
    ");
    $stmt->bind_param("ss", $v, $k);
    $stmt->execute();
  }

  header("Location: admin-settings.php?success=1");
  exit;
}

/* ======================
   CURRENT SHOP STATUS
====================== */
date_default_timezone_set("Asia/Bangkok");
$now = date("H:i");

if ($shop_mode === 'open') {
  $shopOpen = true;
} elseif ($shop_mode === 'closed') {
  $shopOpen = false;
} else {
  $shopOpen = ($now >= $openTime && $now <= $closeTime);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>⚙️ Admin Settings</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #f5f7fa;
}
.container { display: flex; }
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
.sidebar .logo span { color: #38bdf8; }
.nav-menu { padding: 10px; }
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
.nav-item i { margin-right: 10px; }

.main-content {
  flex: 1;
  padding: 25px;
}

.card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,.1);
  max-width: 420px;
  margin-bottom: 25px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  font-weight: 500;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.btn {
  padding: 10px 16px;
  border-radius: 6px;
  border: none;
  background: #38bdf8;
  color: #fff;
  cursor: pointer;
  width: 100%;
}

.success {
  background: #d1fae5;
  color: #065f46;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
}

.shop-status {
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-weight: 500;
  text-align: center;
}
.shop-status.open {
  background: #dcfce7;
  color: #166534;
}
.shop-status.closed {
  background: #fee2e2;
  color: #991b1b;
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

    <div class="nav-item" onclick="location.href='orders-report.php'">
      <i class="fas fa-chart-line"></i> Orders Report
    </div>

    <div class="nav-item" onclick="location.href='admin-menu.php'">
      <i class="fas fa-store"></i> Manage
    </div>
<div class="nav-item" onclick="location.href='admin-register.php'">
      <i class="fas fa-user-plus"></i> Register Admin
    </div>
    <div class="nav-item active">
      <i class="fas fa-cog"></i> Settings
    </div>

    <div class="nav-item" onclick="location.href='logout.php'">
      <i class="fas fa-sign-out-alt"></i> Logout
    </div>
  </div>
</div>

<!-- MAIN -->
<div class="main-content">

<h2>⚙️ Admin Settings</h2>

<?php if (isset($_GET['success'])): ?>
<div class="success">บันทึกข้อมูลเรียบร้อยแล้ว</div>
<?php endif; ?>

<!-- ===== SHOP STATUS ===== -->
<div class="card">
  <div class="shop-status <?= $shopOpen ? 'open' : 'closed' ?>">
    <?= $shopOpen ? '🟢 ตอนนี้ร้านเปิดอยู่' : '🔴 ตอนนี้ร้านปิดอยู่' ?>
  </div>

  <form method="post">

    <div class="form-group">
      <label>เวลาเปิดร้าน</label>
      <input type="time" name="shop_open_time" value="<?= $openTime ?>">
    </div>

    <div class="form-group">
      <label>เวลาปิดร้าน</label>
      <input type="time" name="shop_close_time" value="<?= $closeTime ?>">
    </div>

    <hr style="margin:20px 0">

    <!-- ===== ADMIN PROFILE (ของเดิมคุณ) ===== -->
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username"
             value="<?= htmlspecialchars($admin['username']) ?>" required>
    </div>

    <div class="form-group">
      <label>New Password (เว้นว่างถ้าไม่เปลี่ยน)</label>
      <input type="password" name="password">
    </div>

    <button class="btn">Save Changes</button>

  </form>
</div>

</div>
</div>

</body>
</html>
