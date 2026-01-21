<?php
session_start();
require "config/db.php";

/* =====================
   รับค่าจากฟอร์ม
===================== */
$name  = trim($_POST['name'] ?? '');
$price = $_POST['price'] ?? 0;
$cat   = $_POST['category_id'] ?? 0;
$rec   = isset($_POST['is_recommend']) ? 1 : 0;

/* =====================
   ตรวจข้อมูล
===================== */
if ($name === '' || !is_numeric($price) || $cat == 0) {
    die("ข้อมูลไม่ครบ");
}

/* =====================
   จัดการรูปภาพ
===================== */
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    die("กรุณาเลือกรูปเมนู");
}

$img = $_FILES['image'];
$ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
$allow = ['jpg','jpeg','png','webp'];

if (!in_array($ext, $allow)) {
    die("ไฟล์รูปไม่ถูกต้อง");
}

/* สร้างโฟลเดอร์ถ้ายังไม่มี */
$dir = "uploads/menu/";
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

/* ตั้งชื่อไฟล์ */
$fileName = uniqid("menu_") . "." . $ext;
$path = $dir . $fileName;

if (!move_uploaded_file($img['tmp_name'], $path)) {
    die("อัปโหลดรูปไม่สำเร็จ");
}

/* =====================
   บันทึกลงฐานข้อมูล
===================== */
$stmt = $conn->prepare("
    INSERT INTO menus (name, price, image, category_id, is_recommend, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "sdsii",
    $name,
    $price,
    $path,
    $cat,
    $rec
);

$stmt->execute();

/* =====================
   กลับหน้า admin
===================== */
header("Location: admin-menu.php");
exit;
