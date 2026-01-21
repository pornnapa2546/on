<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

require "config/db.php";

/* =========================
   CHECK POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: admin_menu.php");
  exit;
}

$id    = (int)$_POST['id'];
$name  = $_POST['name'];
$price = $_POST['price'];
$cat   = $_POST['category_id'];
$rec   = isset($_POST['is_recommend']) ? 1 : 0;

/* =========================
   GET OLD DATA
========================= */
$stmt = $conn->prepare("SELECT image FROM menus WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$old = $stmt->get_result()->fetch_assoc();

if (!$old) {
  header("Location: admin-menu.php");
  exit;
}

$imagePath = $old['image'];

/* =========================
   UPLOAD IMAGE (IF NEW)
========================= */
if (!empty($_FILES['image']['name'])) {

  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $allow = ['jpg','jpeg','png','webp'];

  if (!in_array($ext, $allow)) {
    die("Invalid image format");
  }

  if (!is_dir("uploads/menu")) {
    mkdir("uploads/menu", 0777, true);
  }

  // ลบรูปเก่า
  if ($imagePath && file_exists($imagePath)) {
    unlink($imagePath);
  }

  $imagePath = "uploads/menu/" . time() . "_" . uniqid() . "." . $ext;
  move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
}

/* =========================
   UPDATE MENU
========================= */
$stmt = $conn->prepare("
  UPDATE menus
  SET name = ?, price = ?, image = ?, category_id = ?, is_recommend = ?
  WHERE id = ?
");

$stmt->bind_param(
  "sdsiii",
  $name,
  $price,
  $imagePath,
  $cat,
  $rec,
  $id
);

$stmt->execute();

/* =========================
   REDIRECT
========================= */
header("Location: admin-menu.php");
exit;
