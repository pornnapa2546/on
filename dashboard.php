<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
include "config/db.php";

$today = date('Y-m-d');

$totalAll = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT SUM(total) AS sum FROM orders WHERE status='approved'")
)['sum'] ?? 0;

$totalToday = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT SUM(total) AS sum FROM orders 
   WHERE status='approved' AND DATE(created_at)='$today'")
)['sum'] ?? 0;
?>

<h2>📊 Dashboard</h2>
<p>💰 ยอดขายทั้งหมด: <b><?= number_format($totalAll) ?> ฿</b></p>
<p>📅 ยอดขายวันนี้: <b><?= number_format($totalToday) ?> ฿</b></p>

<a href="admin.php">← ดูออเดอร์</a>
