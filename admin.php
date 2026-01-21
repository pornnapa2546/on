<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: admin-login.php");
  exit;
}
include "config/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Orders</title>
  <style>
    body {
      font-family: Arial;
      background: #f5f7fa;
      padding: 20px;
    }
    .order {
      background: #fff;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    img {
      margin-top: 10px;
      max-width: 200px;
      border-radius: 6px;
    }
    button {
      margin-right: 10px;
      padding: 6px 12px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<h1>📦 Order List</h1>

<?php
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");

while ($row = mysqli_fetch_assoc($result)) {
  echo "<div class='order'>";
  echo "<h3>{$row['customer_name']} - {$row['total']} ฿</h3>";
  echo "<p>Phone: {$row['phone']}</p>";
  echo "<p>Address: {$row['address']}</p>";
  echo "<p>Status: <strong>{$row['status']}</strong></p>";
  echo "<img src='uploads/slips/{$row['slip_image']}'>";

  if ($row['status'] === 'pending') {
    echo "
      <form method='post' action='update-status.php'>
        <input type='hidden' name='order_id' value='{$row['id']}'>
        <button name='status' value='approved'>✅ Approve</button>
        <button name='status' value='rejected'>❌ Reject</button>
      </form>
    ";
  }

  echo "</div>";
}
require "config/db.php";
include "layout-header.php";
?>

<h2>📦 Order Management (Today)</h2>

<div id="orders-container"></div>

<script>
function loadOrders() {
  fetch('fetch-orders-today.php')
    .then(res => res.text())
    .then(html => {
      document.getElementById('orders-container').innerHTML = html;
    });
}

// โหลดครั้งแรก
loadOrders();

// รีเฟรชทุก 5 วินาที (Realtime)
setInterval(loadOrders, 5000);

function updateStatus(id, status) {
  fetch('update-status.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: `order_id=${id}&status=${status}`
  }).then(() => loadOrders());
}
</script>

<?php include "layout-footer.php"; ?>
