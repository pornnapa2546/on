<?php
require 'vendor/autoload.php';
include "config/db.php";

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
  die("Order not found");
}

$order_id = (int)$_GET['id'];

$order = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id")
);

if (!$order) {
  die("Order not found");
}

$items_result = mysqli_query(
  $conn,
  "SELECT product_name, price, qty, temp, sweet
   FROM order_items
   WHERE order_id = $order_id"
);

/* ===== LOGO ===== */
$logoPath = __DIR__ . "/img/2.png";
$logoBase64 = '';
if (file_exists($logoPath)) {
  $type = pathinfo($logoPath, PATHINFO_EXTENSION);
  $data = file_get_contents($logoPath);
  $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

/* ===== PDF OPTIONS ===== */
$options = new Options();
$options->set('defaultFont', 'sarabun');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

/* ===== STATUS STYLE ===== */
$statusText  = strtoupper($order['status']);
$statusColor = $order['status'] === 'approved' ? '#16a34a'
              : ($order['status'] === 'rejected' ? '#dc2626' : '#f59e0b');

/* ===== HTML ===== */
$html = "
<style>
body {
  font-family:sarabun;
  font-size:15px;
  color:#111;
}

.header {
  text-align:center;
  margin-bottom:10px;
}
.logo {
  width:90px;
  margin-bottom:6px;
}
.shop-name {
  font-size:20px;
  font-weight:bold;
}
.subtitle {
  font-size:14px;
  color:#555;
}

hr {
  border:none;
  border-top:1px dashed #bbb;
  margin:12px 0;
}

.info p {
  margin:4px 0;
}

.badge {
  display:inline-block;
  padding:4px 10px;
  border-radius:14px;
  background:#f3f4f6;
  font-size:12px;
}

.badge.hot { background:#fee2e2; }
.badge.cold { background:#e0f2fe; }
.badge.sweet { background:#ecfeff; }

.status {
  margin-top:10px;
  padding:6px 14px;
  border-radius:16px;
  font-weight:bold;
  color:#fff;
  display:inline-block;
  background: {$statusColor};
}

table {
  width:100%;
  border-collapse:collapse;
  margin-top:14px;
}
th, td {
  border:1px solid #333;
  padding:8px;
  font-size:14px;
}
th {
  background:#f9fafb;
}
.right { text-align:right; }
.center { text-align:center; }

.footer {
  margin-top:30px;
  text-align:center;
  font-size:14px;
  color:#333;
}
</style>

<div class='header'>
  <img src='{$logoBase64}' class='logo'>
  <div class='shop-name'>☕ On The Way Cafe</div>
  <div class='subtitle'>ใบเสร็จรับเงิน / Receipt</div>
</div>

<hr>

<div class='info'>
  <p>👤 <strong>ลูกค้า:</strong> {$order['customer_name']}</p>
  <p>📞 <strong>เบอร์โทร:</strong> {$order['phone']}</p>
  <p>📅 <strong>วันที่:</strong> {$order['created_at']}</p>
  <p>🧾 <strong>เลขออเดอร์:</strong> {$order['order_no']}</p>
</div>

<table>
<tr>
  <th>🍹 รายการ</th>
  <th>🌡 Temp</th>
  <th>🍬 Sweet</th>
  <th class='right'>💰 ราคา</th>
  <th class='right'>🧮 จำนวน</th>
  <th class='right'>💵 รวม</th>
</tr>
";

$total = 0;

while ($item = mysqli_fetch_assoc($items_result)) {
  $sum = $item['price'] * $item['qty'];
  $total += $sum;

  $tempLabel = $item['temp'] === 'hot'
    ? "<span class='badge hot'>🔥 Hot</span>"
    : "<span class='badge cold'>🧊 Cold</span>";

  $sweetLabel = "<span class='badge sweet'>{$item['sweet']}</span>";

  $html .= "
  <tr>
    <td>{$item['product_name']}</td>
    <td class='center'>{$tempLabel}</td>
    <td class='center'>{$sweetLabel}</td>
    <td class='right'>" . number_format($item['price'],2) . "</td>
    <td class='right'>{$item['qty']}</td>
    <td class='right'>" . number_format($sum,2) . "</td>
  </tr>
  ";
}

$html .= "
<tr>
  <th colspan='5' class='right'>รวมทั้งสิ้น</th>
  <th class='right'>" . number_format($total,2) . "</th>
</tr>
</table>

<div class='center'>
  <div class='status'>สถานะ: {$statusText}</div>
</div>

<div class='footer'>
  🙏 ขอบคุณที่ใช้บริการ <br>
</div>
";

/* ===== RENDER PDF ===== */
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("receipt_order_{$order_id}.pdf", ["Attachment"=>false]);
