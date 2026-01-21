<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

require "config/db.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $confirm  = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $error = "รหัสผ่านไม่ตรงกัน";
  } else {
    // เช็ค username ซ้ำ
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = "Username นี้ถูกใช้แล้ว";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("
        INSERT INTO admins (username, password)
        VALUES (?, ?)
      ");
      $stmt->bind_param("ss", $username, $hash);
      $stmt->execute();

      $success = "เพิ่มแอดมินเรียบร้อยแล้ว";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>👤 Register Admin</title>

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
}

.form-group {
  margin-bottom: 15px;
}
.form-group label {
  font-weight: 500;
}
.form-group input {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.btn {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: none;
  background: #38bdf8;
  color: #fff;
  font-size: 15px;
  cursor: pointer;
}

.error {
  background: #fee2e2;
  color: #991b1b;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
}

.success {
  background: #d1fae5;
  color: #065f46;
  padding: 10px;
  border-radius: 6px;
  margin-bottom: 15px;
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

    <div class="nav-item active">
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

<h2>👤 Register Admin</h2>

<?php if ($error): ?>
  <div class="error"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="success"><?= $success ?></div>
<?php endif; ?>

<div class="card">
<form method="post">

  <div class="form-group">
    <label>Username</label>
    <input type="text" name="username" required>
  </div>

  <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" required>
  </div>

  <div class="form-group">
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" required>
  </div>

  <button class="btn">Register Admin</button>

</form>
</div>

</div>
</div>

</body>
</html>
