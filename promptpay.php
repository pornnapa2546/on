<?php
// =======================
// CONFIG
// =======================
$promptpay_id = "0806297068"; // ❗ เปลี่ยนเป็นเบอร์ / บัตร ปชช. จริง
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;

if ($amount <= 0) {
  http_response_code(400);
  exit("Invalid amount");
}

// =======================
// CRC16
// =======================
function crc16($str) {
  $crc = 0xFFFF;
  for ($i = 0; $i < strlen($str); $i++) {
    $crc ^= ord($str[$i]) << 8;
    for ($j = 0; $j < 8; $j++) {
      $crc = ($crc & 0x8000) ? ($crc << 1) ^ 0x1021 : $crc << 1;
      $crc &= 0xFFFF;
    }
  }
  return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}

// =======================
// GENERATE PAYLOAD
// =======================
function generatePromptPayPayload($id, $amount) {
  $amount = number_format($amount, 2, '.', '');

  $payload =
    "000201010212" .
    "29370016A000000677010111" .
    "0113" . str_pad($id, 13, "0", STR_PAD_LEFT) .
    "5802TH" .
    "5303764" .
    "54" . str_pad(strlen($amount), 2, '0', STR_PAD_LEFT) . $amount .
    "6304";

  return $payload . crc16($payload);
}

$payload = generatePromptPayPayload($promptpay_id, $amount);

// =======================
// OUTPUT IMAGE (สำคัญที่สุด)
// =======================
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($payload);

header("Location: $qr_api");
exit;
