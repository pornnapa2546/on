<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
require "config/db.php";

/* ======================
   LOAD MENU
====================== */
if (!isset($_GET['id'])) {
  header("Location: admin-menu.php");
  exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("
  SELECT * FROM menus WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$menu = $stmt->get_result()->fetch_assoc();

if (!$menu) {
  header("Location: admin-menu.php");
  exit;
}

/* ======================
   LOAD CATEGORIES
====================== */
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>✏️ Edit Menu</title>

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
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
  gap: 15px;
}
input, select {
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  width: 100%;
}
.menu-img {
  width: 120px;
  border-radius: 10px;
  margin-bottom: 10px;
}
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}
.btn-primary {
  background: #38bdf8;
  color: #fff;
}
.btn-outline {
  border: 1px solid #38bdf8;
  color: #38bdf8;
  background: none;
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
  <div class="nav-item <?= ($_GET['view'] ?? '')=='day'?'active':'' ?>"
       onclick="location.href='orders-by-day.php?view=day'">
    <i class="fas fa-calendar-day"></i> Orders by Day
  </div>

  <div class="nav-item <?= ($_GET['view'] ?? '')=='month'?'active':'' ?>"
       onclick="location.href='orders-by-month.php?view=month'">
    <i class="fas fa-calendar-alt"></i> Orders by Month
  </div>
  <div class="nav-item <?= ($_GET['view'] ?? '')=='year'?'active':'' ?>"
       onclick="location.href='orders-by-year.php?view=year'">
    <i class="fas fa-calendar"></i> Orders by Year
  </div>
  <div class="nav-item" onclick="location.href='admin-menu.php'">
      <i class="fas fa-shopping-cart"></i> Manage
    </div>

    <div class="nav-item" onclick="location.href='logout.php'">
      <i class="fas fa-sign-out-alt"></i> Logout
    </div>
  </div>
</div>

<!-- MAIN -->
<div class="main-content">

<h2>✏️ Edit Menu</h2>

<div class="card">

<form action="menu-update.php" method="post" enctype="multipart/form-data">

  <input type="hidden" name="id" value="<?= $menu['id'] ?>">
  <input type="hidden" name="old_image" value="<?= $menu['image'] ?>">

  <div class="form-grid">

    <div>
      <label>Menu Name</label>
      <input type="text" name="name"
             value="<?= htmlspecialchars($menu['name']) ?>" required>
    </div>

    <div>
      <label>Price</label>
      <input type="number" name="price"
             value="<?= $menu['price'] ?>" required>
    </div>

    <div>
      <label>Category</label>
      <select name="category_id" required>
        <?php while($c = $categories->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"
            <?= $menu['category_id']==$c['id']?'selected':'' ?>>
            <?= htmlspecialchars($c['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div>
      <label>
        <input type="checkbox" name="is_recommend"
          <?= $menu['is_recommend'] ? 'checked':'' ?>>
        Recommend
      </label>
    </div>

  </div>

  <br>

  <label>Current Image</label><br>
  <img src="<?= $menu['image'] ?>" class="menu-img"><br><br>

  <label>Change Image (optional)</label>
  <input type="file" name="image">

  <br><br>

 <button type="submit" class="btn btn-primary">
  <i class="fas fa-save"></i> Update Menu
</button>


  <a href="admin-menu.php" class="btn btn-outline">
    Cancel
  </a>

</form>

</div>
</div>
</div>

</body>
</html>
