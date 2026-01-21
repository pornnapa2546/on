<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

require "config/db.php";

$id = (int)$_GET['id'];

// ลบรูปก่อน (ถ้ามี)
$stmt = $conn->prepare("SELECT image FROM menus WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$img = $stmt->get_result()->fetch_assoc();

if ($img && file_exists($img['image'])) {
  unlink($img['image']);
}

// ลบเมนู
$stmt = $conn->prepare("DELETE FROM menus WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

// 🔥 redirect ให้ตรง
header("Location: /project/admin-menu.php");
exit;
