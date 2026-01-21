<?php
session_start();

/* =========================
   AUTH ADMIN
========================= */
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit('FORBIDDEN');
}

require __DIR__ . '/config/db.php';
require __DIR__ . '/line/line-message.php'; // ฟังก์ชันส่ง LINE

/* =========================
   VALIDATE INPUT
========================= */
if (!isset($_POST['order_id'], $_POST['status'])) {
    http_response_code(400);
    exit('INVALID_REQUEST');
}

$order_id = (int)$_POST['order_id'];
$status   = $_POST['status'];

if (!in_array($status, ['approved', 'rejected'])) {
    http_response_code(400);
    exit('INVALID_STATUS');
}

/* =========================
   UPDATE ORDER STATUS
========================= */
if ($status === 'approved') {

    $stmt = $conn->prepare("
        UPDATE orders
        SET status = 'approved',
            approved_at = NOW()
        WHERE id = ? AND status = 'pending'
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        exit('ORDER_NOT_PENDING');
    }

} elseif ($status === 'rejected') {

    $stmt = $conn->prepare("
        UPDATE orders
        SET status = 'rejected',
            rejected_at = NOW()
        WHERE id = ? AND status = 'pending'
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        exit('ORDER_NOT_PENDING');
    }
}

/* =========================
   LOAD ORDER DATA
========================= */
$q = $conn->prepare("
    SELECT order_no, line_user_id, total
    FROM orders
    WHERE id = ?
");
$q->bind_param("i", $order_id);
$q->execute();
$order = $q->get_result()->fetch_assoc();

/* =========================
   SEND LINE NOTIFICATION
========================= */
if (!empty($order['line_user_id'])) {

    if ($status === 'approved') {

        $msg  = "✅ ออเดอร์ได้รับการยืนยันแล้ว\n";
        $msg .= "ขอบคุณที่ใช้บริการ ☕🙏\n";
        $msg .= "📄 ใบเสร็จอยู่ด้านล่าง";

        // 🔒 ฟิก ngrok URL (สำคัญมาก)
        $siteUrl = "https://b49005e06d39.ngrok-free.app/project";

        // ✅ ส่งข้อความ (ครั้งเดียว)
        sendLineMessage($order['line_user_id'], $msg);

        // ✅ ส่งรูปใบเสร็จ PNG
        $imageUrl = "{$siteUrl}/receipt-image.php?id={$order_id}";
        sendLineImage($order['line_user_id'], $imageUrl);

    } else { // rejected

        $msg  = "❌ ออเดอร์ถูกปฏิเสธ\n";
        $msg .= "เลขออเดอร์: {$order['order_no']}\n\n";
        $msg .= "กรุณาติดต่อร้านหากมีข้อสงสัย\n";
        $msg .= "ขออภัยในความไม่สะดวก 🙏";

        sendLineMessage($order['line_user_id'], $msg);
    }
}


/* =========================
   RESPONSE
========================= */
echo "OK";
exit;
