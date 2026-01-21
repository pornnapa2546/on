<?php
require __DIR__ . '/config/db.php';

/* =========================
   VALIDATE
========================= */
$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
    http_response_code(404);
    exit;
}

/* =========================
   LOAD ORDER
========================= */
$order = $conn->query("
    SELECT *
    FROM orders
    WHERE id = {$order_id}
")->fetch_assoc();

if (!$order) {
    http_response_code(404);
    exit;
}

/* =========================
   LOAD ITEMS
========================= */
$items = $conn->query("
    SELECT product_name, price, qty
    FROM order_items
    WHERE order_id = {$order_id}
");

/* =========================
   CANVAS
========================= */
$width  = 900;
$height = 1200;
$img = imagecreatetruecolor($width, $height);

/* COLORS */
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
$gray  = imagecolorallocate($img, 120, 120, 120);
$line  = imagecolorallocate($img, 210, 210, 210);
$green = imagecolorallocate($img, 22, 163, 74);

imagefill($img, 0, 0, $white);

/* =========================
   FONT
========================= */
$font = __DIR__ . '/fonts/THSarabunNew.ttf';

/* =========================
   LOGO
========================= */
$logoPath = __DIR__ . '/img/2.png';
if (file_exists($logoPath)) {
    $logo = imagecreatefrompng($logoPath);
    imagecopyresampled(
        $img,
        $logo,
        380, 30,
        0, 0,
        180, 180,
        imagesx($logo),
        imagesy($logo)
    );
}

/* =========================
   HEADER
========================= */



imagettftext($img, 34, 0, 350, 260, $black, $font, "On The Way Cafe");
imagettftext($img, 22, 0, 370, 300, $gray,  $font, "ใบเสร็จรับเงิน / Receipt");

imageline($img, 80, 330, 820, 330, $line);

/* =========================
   CUSTOMER INFO
========================= */
$y = 380;
imagettftext($img, 24, 0, 100, $y, $black, $font, "ลูกค้า: {$order['customer_name']}");
$y += 36;
imagettftext($img, 24, 0, 100, $y, $black, $font, "เบอร์โทร: {$order['phone']}");
$y += 36;
imagettftext($img, 24, 0, 100, $y, $black, $font, "วันที่: {$order['created_at']}");
$y += 36;
imagettftext($img, 24, 0, 100, $y, $black, $font, "เลขออเดอร์: {$order['order_no']}");

imageline($img, 80, $y + 20, 820, $y + 20, $line);

/* =========================
   TABLE HEADER
========================= */
$y += 70;

imagettftext($img, 24, 0, 100, $y, $black, $font, "รายการ");
imagettftext($img, 24, 0, 500, $y, $black, $font, "ราคา");
imagettftext($img, 24, 0, 620, $y, $black, $font, "จำนวน");
imagettftext($img, 24, 0, 740, $y, $black, $font, "รวม");

imageline($img, 80, $y + 15, 820, $y + 15, $line);

/* =========================
   TABLE BODY
========================= */
$total = 0;
$y += 50;

while ($item = $items->fetch_assoc()) {

    $sum = $item['price'] * $item['qty'];
    $total += $sum;

    imagettftext($img, 22, 0, 100, $y, $black, $font, $item['product_name']);
    imagettftext($img, 22, 0, 500, $y, $black, $font, number_format($item['price'], 2));
    imagettftext($img, 22, 0, 640, $y, $black, $font, $item['qty']);
    imagettftext($img, 22, 0, 740, $y, $black, $font, number_format($sum, 2));

    $y += 44;
}

/* =========================
   TOTAL
========================= */
imageline($img, 80, $y + 10, 820, $y + 10, $line);
$y += 50;

imagettftext($img, 26, 0, 540, $y, $black, $font, "รวมทั้งสิ้น");
imagettftext($img, 26, 0, 740, $y, $green, $font, number_format($total, 2) . " บาท");

/* =========================
   FOOTER
========================= */
$y += 80;
imagettftext($img, 30, 0, 380, $y, $gray, $font, "ขอบคุณที่ใช้บริการ");

/* =========================
   OUTPUT
========================= */
header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);
exit;
