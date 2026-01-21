<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
require "config/db.php";

/* ======================
   LOAD DATA
====================== */
$menus = $conn->query("
  SELECT m.*, c.name AS category_name
  FROM menus m
  LEFT JOIN categories c ON m.category_id = c.id
  ORDER BY m.id DESC
");

$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🍹 Manage Menu</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #f5f7fa;
}
.container { display: flex; }

/* SIDEBAR */
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
  border-radius: 6px;
  margin-bottom: 8px;
  cursor: pointer;
}
.nav-item:hover,
.nav-item.active { background: #334155; }
.nav-item i { margin-right: 10px; }

/* MAIN */
.main-content {
  flex: 1;
  padding: 25px;
}
.card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,.1);
  margin-bottom: 25px;
}
.card h3 { margin-bottom: 15px; }

/* FORM */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(200px,1fr));
  gap: 5px;
}
input, select {
  padding: 15px;
  border-radius: 6px;
  border: 1px solid #ccc;
  width: 80%;
}
.btn {
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  border: none;
  cursor: pointer;
}
.btn-primary {
  background: #38bdf8;
  color: #fff;
}
.btn-danger {
  background: #ef4444;
  color: #fff;
}
.btn-outline {
  border: 1px solid #38bdf8;
  color: #38bdf8;
  background: none;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
th { background: #f1f5f9; }
img.menu-img {
  width: 60px;
  border-radius: 6px;
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
    <div class="nav-item" onclick="location.href='admin-settings.php'">
  <i class="fas fa-cog"></i> Settings
</div>

    <div class="nav-item" onclick="location.href='logout.php'">
      <i class="fas fa-sign-out-alt"></i> Logout
    </div>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<h2>🍹 Manage Menu</h2>

<!-- ADD MENU -->
<div class="card">
<h3>➕ Add New Menu</h3>

<form action="menu-save.php" method="post" enctype="multipart/form-data">
  <div class="form-grid">
    <input type="text" name="name" placeholder="Menu Name" required>
    <input type="number" name="price" placeholder="Price" required>

    <select name="category_id" required>
      <option value="">-- Category --</option>
      <?php while($c = $categories->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
      <?php endwhile; ?>
    </select>

    <input type="file" name="image" required>

    <label>
      <input type="checkbox" name="is_recommend"> Recommend
    </label>
  </div>

  <br>
  <button class="btn btn-primary">
    <i class="fas fa-save"></i> Save Menu
  </button>
</form>
</div>

<!-- MENU LIST -->
<div class="card">
<h3>📋 Menu List</h3>

<table>
<thead>
<tr>
  <th>Image</th>
  <th>Name</th>
  <th>Category</th>
  <th>Price</th>
  <th>Recommend</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php while($m = $menus->fetch_assoc()): ?>
<tr>
  <td>
    <img src="<?= $m['image'] ?>" class="menu-img">
  </td>
  <td><?= htmlspecialchars($m['name']) ?></td>
  <td><?= htmlspecialchars($m['category_name']) ?></td>
  <td><?= number_format($m['price'],0) ?> ฿</td>
  <td><?= $m['is_recommend'] ? '⭐' : '-' ?></td>
  <td>
    <a href="menu-edit.php?id=<?= $m['id'] ?>" class="btn btn-outline">
      Edit
    </a>
    <a href="menu-delete.php?id=<?= $m['id'] ?>"
       class="btn btn-danger"
       onclick="return confirm('Delete this menu?')">
      Delete
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
