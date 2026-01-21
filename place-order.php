<?php
session_start();
require "config/db.php";
require "functions.php";


$customer = $_POST['customer_name'];
$phone    = $_POST['phone'];
$total    = $_POST['total'];
$cart     = $_POST['cart']; // array

$orderNo = generateOrderNo($conn);

$stmt = $conn->prepare("
  INSERT INTO orders
  (order_no, customer_name, phone, total, status, created_at)
  VALUES (?, ?, ?, ?, 'pending', NOW())
");
$stmt->bind_param("sssd", $orderNo, $customer, $phone, $total);
$stmt->execute();

$order_id = $stmt->insert_id;

$itemStmt = $conn->prepare("
  INSERT INTO order_items
  (order_id, product_name, price, qty)
  VALUES (?, ?, ?, ?)
");

foreach ($cart as $item) {
  $itemStmt->bind_param(
    "isdi",
    $order_id,
    $item['name'],
    $item['price'],
    $item['qty']
  );
  $itemStmt->execute();
}

echo "SUCCESS";
