<?php
function generateReceiptImage($order, $items) {

  if (!is_dir("receipt")) {
    mkdir("receipt", 0777, true);
  }

  $img = imagecreatetruecolor(600, 800);
  $white = imagecolorallocate($img, 255,255,255);
  $black = imagecolorallocate($img, 0,0,0);
  imagefill($img, 0, 0, $white);

  $y = 30;
  imagestring($img, 5, 180, $y, "OTW Cafe Receipt", $black);
  $y += 40;

  imagestring($img, 3, 20, $y, "Order No: {$order['order_no']}", $black);
  $y += 25;
  imagestring($img, 3, 20, $y, "Customer: {$order['customer_name']}", $black);
  $y += 25;
  imagestring($img, 3, 20, $y, "Phone: {$order['phone']}", $black);
  $y += 30;

  imagestring($img, 4, 20, $y, "Items:", $black);
  $y += 25;

  foreach ($items as $it) {
    imagestring(
      $img,
      3,
      20,
      $y,
      "- {$it['product_name']} x{$it['qty']} = {$it['price']} ฿",
      $black
    );
    $y += 20;
  }

  $y += 20;
  imagestring($img, 4, 20, $y, "Total: {$order['total']} ฿", $black);

  $file = "receipt/receipt_{$order['order_no']}.png";
  imagepng($img, $file);
  imagedestroy($img);

  return $file;
}
