<?php
function generateOrderNo($conn) {

  $date = date('Ymd');
  $prefix = "OTW-$date";

  $sql = "
    SELECT order_no
    FROM orders
    WHERE order_no LIKE '$prefix%'
    ORDER BY order_no DESC
    LIMIT 1
  ";

  $res = $conn->query($sql);

  if ($res->num_rows > 0) {
    $last = $res->fetch_assoc()['order_no'];
    $num = (int) substr($last, -3) + 1;
  } else {
    $num = 1;
  }

  return $prefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
}
