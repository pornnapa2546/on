<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
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
