<?php
require "config/db.php";

/* =======================
   GENERATE ORDER NO
======================= */
$today = date('Ymd');

$result = $conn->query("
  SELECT COUNT(*) AS total 
  FROM orders 
  WHERE DATE(created_at) = CURDATE()
");
$row = $result->fetch_assoc();
$run = str_pad($row['total'] + 1, 3, '0', STR_PAD_LEFT);

$order_no = "OTW-$today-$run";

/* =======================
   INSERT ORDER
======================= */
$stmt = $conn->prepare("
  INSERT INTO orders
  (order_no, customer_name, phone, total, status, created_at)
  VALUES (?, ?, ?, ?, 'pending', NOW())
");

$stmt->bind_param(
  "sssd",
  $order_no,
  $_POST['customer_name'],
  $_POST['phone'],
  $_POST['total']
);

$stmt->execute();

$order_id = $stmt->insert_id;

/* 👉 insert order_items ต่อได้ตามระบบเดิม */

echo "SUCCESS";
