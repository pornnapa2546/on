<?php
session_start();

/* ถ้า Login แล้ว ไม่ต้องอยู่หน้านี้ */
if (!empty($_SESSION['line_user_id'])) {
  header("Location: order.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>แอด LINE ก่อนสั่ง</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
/* ===== BASE ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: #f0fdf4;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  margin: 0;
  padding: 20px;
}

.box {
  background: #fff;
  padding: 40px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0,0,0,.1);
  max-width: 420px;
  width: 100%;
}

.box img.logo {
  width: 90px;
  margin-bottom: 20px;
}

.box h2 {
  font-size: 24px;
  margin-bottom: 12px;
}

.sub {
  color: #555;
  font-size: 16px;
  line-height: 1.6;
}

.qr {
  width: 220px;
  max-width: 100%;
  margin: 24px auto;
}

.btn {
  background: #06c755;
  color: #fff;
  padding: 16px 28px;
  border-radius: 999px;
  text-decoration: none;
  font-size: 18px;
  display: inline-block;
  margin-top: 10px;
}

.btn:hover {
  opacity: .9;
}

.login-link {
  display: block;
  margin-top: 22px;
  color: #06c755;
  font-size: 16px;
  text-decoration: underline;
}

/* ===== MOBILE (จอเล็ก) ===== */
@media (max-width: 480px) {

  .box {
    padding: 32px 24px;
    border-radius: 22px;
  }

  .box h2 {
    font-size: 26px;
  }

  .sub {
    font-size: 17px;
  }

  .qr {
    width: 260px;   /* ขยาย QR ให้สแกนง่าย */
  }

  .btn {
    width: 100%;
    font-size: 20px;
    padding: 18px;
  }

  .login-link {
    font-size: 17px;
  }
}
</style>
</head>

<body>

<div class="box">
  <img src="img/line-logo.png" alt="LINE" class="logo">

  <h2>กรุณาแอด LINE ร้านก่อนสั่งซื้อ</h2>

  <p class="sub">
    เพื่อรับการแจ้งเตือนสถานะออเดอร์<br>
    และใบเสร็จหลังร้านยืนยัน
  </p>

  <img src="img/line.png" alt="LINE QR" class="qr">

  <a href="/project/line/line-login.php" class="login-link">
    ฉันแอดแล้ว → Login ด้วย LINE
  </a>
</div>

</body>
</html>
