<?php
session_start();

require "config/db.php";
require "config/line-config.php";
require "line/line-notify.php";

/* ==========================
   CHECK LINE LOGIN
========================== */
$lineUserId = $_SESSION['line_user_id'] ?? null;
if (!$lineUserId) {
  http_response_code(401);
  exit("LINE LOGIN REQUIRED");
}

/* ==========================
   FUNCTION : GENERATE ORDER NO
========================== */
function generateOrderNo($conn) {
  $today = date('Ymd');

  $stmt = $conn->prepare("
    SELECT order_no
    FROM orders
    WHERE order_no LIKE CONCAT('OTW-', ?, '-%')
    ORDER BY order_no DESC
    LIMIT 1
  ");
  $stmt->bind_param("s", $today);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $last = (int)substr($row['order_no'], -4);
    $new  = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
  } else {
    $new = '0001';
  }

  return "OTW-$today-$new";
}

/* ==========================
   RECEIVE DATA
========================== */
$customer = trim($_POST['customer_name'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$total    = (float)($_POST['total'] ?? 0);
$cart     = json_decode($_POST['cart'] ?? '[]', true);

if (!$customer || !$phone || empty($cart)) {
  http_response_code(400);
  exit("INVALID DATA");
}

/* ==========================
   UPLOAD SLIP
========================== */
$slipFileName = null;

if (!empty($_FILES['slip']['name'])) {

  $allowed = ['jpg','jpeg','png','webp'];
  $ext = strtolower(pathinfo($_FILES['slip']['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed)) {
    http_response_code(400);
    exit("INVALID SLIP FORMAT");
  }

  if (!is_dir("uploads/slips")) {
    mkdir("uploads/slips", 0777, true);
  }

  $slipFileName = uniqid("slip_") . "." . $ext;
  move_uploaded_file(
    $_FILES['slip']['tmp_name'],
    "uploads/slips/" . $slipFileName
  );
}



/* ==========================
   START TRANSACTION
========================== */
$conn->begin_transaction();

try {

  $orderNo = generateOrderNo($conn);

  /* ===== INSERT ORDER ===== */
  $stmt = $conn->prepare("
    INSERT INTO orders
    (order_no, customer_name, phone, total, slip_image, line_user_id, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
  ");
  $stmt->bind_param(
    "sssdss",
    $orderNo,
    $customer,
    $phone,
    $total,
    $slipFileName,
    $lineUserId
  );
  $stmt->execute();

  $order_id = $stmt->insert_id;

  /* ===== INSERT ORDER ITEMS ===== */
  $itemStmt = $conn->prepare("
    INSERT INTO order_items
    (order_id, product_name, price, temp, sweet, qty)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  foreach ($cart as $item) {
    $productName = $item['name']; // ✅ แก้ให้ตรงกับ JS
    $price = (float)$item['price'];
    $temp  = $item['temp'];
    $sweet = $item['sweet'];
    $qty   = (int)$item['qty'];

    $itemStmt->bind_param(
      "isdssi",
      $order_id,
      $productName,
      $price,
      $temp,
      $sweet,
      $qty
    );
    $itemStmt->execute();
  }

  $conn->commit();

  /* ==========================
     🔔 LINE แจ้งร้าน
  ========================== */
  $msgAdmin  = "📦 มีออเดอร์ใหม่\n";
  $msgAdmin .= "เลขออเดอร์: $orderNo\n";
  $msgAdmin .= "ลูกค้า: $customer\n";
  $msgAdmin .= "ยอดรวม: $total บาท\n\n";

  foreach ($cart as $item) {
    $msgAdmin .= "- {$item['name']} ({$item['temp']} / {$item['sweet']}) x{$item['qty']}\n";
  }

  sendLineNotify($msgAdmin);

  /* ==========================
     🔔 LINE แจ้งลูกค้า
  ========================== */
  sendLineMessage(
    $lineUserId,
    "🧾 รับออเดอร์เรียบร้อย\nเลขออเดอร์: $orderNo\nรอร้านตรวจสอบ ☕"
  );

  echo json_encode([
    "status"   => "success",
    "order_no" => $orderNo
  ]);

} catch (Exception $e) {

  $conn->rollback();
  http_response_code(500);

  echo json_encode([
    "status" => "error",
    "msg"    => "ไม่สามารถบันทึกออเดอร์ได้"
  ]);
}
