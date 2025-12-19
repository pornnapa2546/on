<?php
include "config/db.php";

$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];
$total = $_POST['total'];

$slip = $_FILES['slip'];
$filename = time() . "_" . $slip['name'];
move_uploaded_file($slip['tmp_name'], "uploads/slips/" . $filename);

// insert order
$sql = "INSERT INTO orders (customer_name, phone, email, address, total, slip_image)
        VALUES ('$name','$phone','$email','$address','$total','$filename')";
mysqli_query($conn, $sql);

$order_id = mysqli_insert_id($conn);

// รับ cart จาก JS
$items = json_decode($_POST['items'], true);

foreach ($items as $item) {
  mysqli_query($conn,"
    INSERT INTO order_items (order_id, product_name, price, qty, total)
    VALUES (
      $order_id,
      '{$item['name']}',
      {$item['price']},
      {$item['qty']},
      {$item['total']}
    )
  ");
}

echo "success";
