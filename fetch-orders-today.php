<?php
require "config/db.php";

/* ==========================
   FETCH TODAY ORDERS
========================== */
$orders = $conn->query("
  SELECT *
  FROM orders
  WHERE DATE(created_at) = CURDATE()
  ORDER BY created_at DESC
");

while ($order = $orders->fetch_assoc()):
?>

<div class="order">

  <div class="order-header">
    <h3>🧾 <?= htmlspecialchars($order['order_no']) ?></h3>
    <span class="status <?= $order['status'] ?>">
      <?= strtoupper($order['status']) ?>
    </span>
  </div>

  <p>
    👤 <?= htmlspecialchars($order['customer_name']) ?> |
    ☎ <?= htmlspecialchars($order['phone']) ?><br>
    ⏰ <?= date("H:i:s", strtotime($order['created_at'])) ?> |
    💰 <?= number_format($order['total'],2) ?> บาท
  </p>

  <!-- ===== SLIP IMAGE ===== -->
  <?php if (!empty($order['slip_image'])): ?>
    <div style="margin:12px 0;">
      <strong>💳 สลิปการโอน</strong><br>
      <img
        src="uploads/slips/<?= htmlspecialchars($order['slip_image']) ?>"
        style="max-width:220px;
               border-radius:10px;
               border:1px solid #ddd;
               cursor:pointer;
               box-shadow:0 4px 10px rgba(0,0,0,.15);"
        onclick="window.open(this.src)"
        alt="Slip Image"
      >
    </div>
  <?php else: ?>
    <p style="color:#e11d48;">❌ ไม่พบสลิป</p>
  <?php endif; ?>

  <!-- ===== ORDER ITEMS ===== -->
  <table>
    <tr>
      <th>สินค้า</th>
      <th>ราคา</th>
      <th>Temp</th>
      <th>Sweet</th>
      <th>จำนวน</th>
      <th>รวม</th>
    </tr>

    <?php
    $items = $conn->query("
      SELECT product_name, price, temp, sweet, qty
      FROM order_items
      WHERE order_id = {$order['id']}
    ");

    $sum = 0;
    while ($item = $items->fetch_assoc()):
      $line = $item['price'] * $item['qty'];
      $sum += $line;
    ?>
    <tr>
      <td><?= htmlspecialchars($item['product_name']) ?></td>
      <td><?= number_format($item['price'],2) ?></td>
      <td><?= $item['temp'] === 'hot' ? '🔥 Hot' : '🧊 Cold' ?></td>
      <td><?= htmlspecialchars($item['sweet']) ?></td>
      <td><?= $item['qty'] ?></td>
      <td><?= number_format($line,2) ?></td>
    </tr>
    <?php endwhile; ?>

    <tr>
      <th colspan="5" style="text-align:right;">รวม</th>
      <th><?= number_format($sum,2) ?></th>
    </tr>
  </table>

  <!-- ===== ACTION ===== -->
  <?php if ($order['status'] === 'pending'): ?>
    <button class="btn-approve"
      onclick="updateStatus(<?= $order['id'] ?>,'approved')">
      ✔ Approve
    </button>

    <button class="btn-reject"
      onclick="updateStatus(<?= $order['id'] ?>,'rejected')">
      ✖ Reject
    </button>
  <?php else: ?>
    <button class="btn-receipt"
      onclick="window.open('receipt.php?id=<?= $order['id'] ?>')">
      🧾 ใบเสร็จ
    </button>
  <?php endif; ?>

</div>

<?php endwhile; ?>
