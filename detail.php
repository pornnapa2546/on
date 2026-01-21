<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

require "config/db.php";

if (!isset($_GET['id'])) {
  header("Location: admin-dashboard.php");
  exit;
}

$order_id = (int)$_GET['id'];

$order = $conn->query("
  SELECT *
  FROM orders
  WHERE id = $order_id
")->fetch_assoc();

if (!$order) {
  echo "Order not found";
  exit;
}

$items = $conn->query("
  SELECT product_name, price, qty, temp, sweet
  FROM order_items
  WHERE order_id = $order_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🧾 Order Detail</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body { font-family:'Poppins',sans-serif; background:#f5f7fa; margin:0; }
.container { padding:30px; }
.card {
  background:#fff;
  padding:25px;
  border-radius:12px;
  box-shadow:0 2px 10px rgba(0,0,0,.1);
  max-width:900px;
  margin:auto;
}
.header {
  display:flex;
  justify-content:space-between;
  margin-bottom:20px;
}
.status { font-weight:600; }
.status.pending { color:orange; }
.status.approved { color:green; }
.status.rejected { color:red; }

table {
  width:100%;
  border-collapse:collapse;
  margin-top:15px;
}
th,td {
  padding:10px;
  border-bottom:1px solid #ddd;
}
th { background:#f1f5f9; text-align:center; }
td.num { text-align:right; }
td.center { text-align:center; }

.back-btn {
  display:inline-block;
  margin-top:20px;
  padding:8px 14px;
  background:#007bff;
  color:#fff;
  text-decoration:none;
  border-radius:6px;
}
</style>
</head>

<body>
<div class="container">
<div class="card">

  <div class="header">
    <h2><?= $order['order_no'] ?></h2>
    <div class="status <?= $order['status'] ?>">
      <?= strtoupper($order['status']) ?>
    </div>
  </div>

  <p>
    👤 <strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
    ☎ <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
    📅 <strong>Date:</strong> <?= $order['created_at'] ?><br>
    💰 <strong>Total:</strong> <?= number_format($order['total'],2) ?> ฿
  </p>

  <h3>📦 Order Items</h3>

  <table>
    <tr>
      <th>Product</th>
      <th>Temp</th>
      <th>Sweet</th>
      <th>Price</th>
      <th>Qty</th>
      <th>Total</th>
    </tr>

    <?php
    $sum = 0;
    while ($item = $items->fetch_assoc()):
      $line = $item['price'] * $item['qty'];
      $sum += $line;
    ?>
    <tr>
      <td><?= htmlspecialchars($item['product_name']) ?></td>
      <td class="center"><?= $item['temp'] === 'hot' ? '🔥 Hot' : '🧊 Cold' ?></td>
      <td class="center"><?= htmlspecialchars($item['sweet']) ?></td>
      <td class="num"><?= number_format($item['price'],2) ?></td>
      <td class="center"><?= $item['qty'] ?></td>
      <td class="num"><?= number_format($line,2) ?></td>
    </tr>
    <?php endwhile; ?>

    <tr>
      <th colspan="5" style="text-align:right;">Grand Total</th>
      <th class="num"><?= number_format($sum,2) ?> ฿</th>
    </tr>
  </table>

  <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</div>
</body>
</html>
