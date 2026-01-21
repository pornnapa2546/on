<?php
session_start();
if (!isset($_SESSION['admin'])) {
  exit;
}

require "config/db.php";
require "line/line-message.php";
require "receipt/generate-receipt.php";

/* ===== รับ id ===== */
$order_id = (int)($_GET['id'] ?? 0);

/* ===== อัปเดตสถานะ ===== */
$conn->query("
  UPDATE orders
  SET status = 'approved'
  WHERE id = $order_id
");

/* ===== ดึง order ===== */
$order = $conn->query("
  SELECT * FROM orders WHERE id = $order_id
")->fetch_assoc();

/* ===== ดึง items ===== */
$items = [];
$q = $conn->query("
  SELECT * FROM order_items WHERE order_id = $order_id
");
while ($row = $q->fetch_assoc()) {
  $items[] = $row;
}

/* ===== สร้างใบเสร็จ ===== */
$receiptPath = generateReceiptImage($order, $items);
$receiptUrl  = "https://your-domain.com/project/" . $receiptPath;

/* ===== ส่ง LINE ลูกค้า ===== */
if (!empty($order['line_user_id'])) {

  sendLineMessage($order['line_user_id'], [
    [
      "type" => "text",
      "text" =>
        "✅ ออเดอร์ได้รับการยืนยันแล้ว\n" .
        "เลขออเดอร์: {$order['order_no']}\n" .
        "ยอดรวม: {$order['total']} บาท\n\n" .
        "📎 ใบเสร็จอยู่ด้านล่าง"
    ],
    [
      "type" => "image",
      "originalContentUrl" => $receiptUrl,
      "previewImageUrl"   => $receiptUrl
    ]
  ]);
}

/* ===== กลับหน้า admin ===== */
header("Location: admin.php");
exit;
