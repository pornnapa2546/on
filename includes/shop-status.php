<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require __DIR__ . "/../config/db.php";

date_default_timezone_set("Asia/Bangkok");

/* โหลด settings */
$rows = $conn->query("SELECT * FROM settings");
$settings = [];
while ($r = $rows->fetch_assoc()) {
  $settings[$r['name']] = $r['value'];
}

$manual   = $settings['shop_manual_status'] ;
$openTime = $settings['shop_open_time'] ;
$closeTime= $settings['shop_close_time'];

$now = date("H:i");

/* คำนวณสถานะร้าน */
if ($manual === 'open') {
  $SHOP_OPEN = true;
} elseif ($manual === 'closed') {
  $SHOP_OPEN = false;
} else {
  $SHOP_OPEN = ($now >= $openTime && $now <= $closeTime);
}
