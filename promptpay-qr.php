<?php
require __DIR__ . '/vendor/autoload.php';

use PromptPayQR\PromptPayQR;

$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$promptpayId = '0806297068'; // ❗ ใส่ PromptPay จริง

$qr = new PromptPayQR();
$qr->setPromptPayID($promptpayId);
$qr->setAmount($amount);

header('Content-Type: image/png');
echo $qr->generate();
