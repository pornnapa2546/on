<?php
session_start();

if (empty($_SESSION['line_user_id'])) {
  header("Location: /project/require-line.php");
  exit;
}

require "config/db.php";

/* ===== LOAD SHOP SETTINGS ===== */
$settings = [];
$r = $conn->query("SELECT * FROM settings");
while ($row = $r->fetch_assoc()) {
  $settings[$row['name']] = $row['value'];
}

$mode      = $settings['shop_manual_status'] ?? 'auto';
$openTime  = $settings['shop_open_time'] ?? '08:00';
$closeTime = $settings['shop_close_time'] ?? '18:00';

date_default_timezone_set("Asia/Bangkok");
$now = date("H:i");

/* ===== CHECK SHOP STATUS ===== */
if ($mode === 'open') {
  $shopOpen = true;
} elseif ($mode === 'closed') {
  $shopOpen = false;
} else {
  if ($openTime < $closeTime) {
    $shopOpen = ($now >= $openTime && $now < $closeTime);
  } else {
    $shopOpen = ($now >= $openTime || $now < $closeTime);
  }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>On the way online</title>

<link rel="shortcut icon" href="img/favicon.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/hover-min.css">
<link rel="stylesheet" href="css/style.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.shop-status {
  margin: 15px auto;
  padding: 12px;
  max-width: 400px;
  text-align: center;
  border-radius: 8px;
  font-weight: 500;
}
.shop-open { background:#dcfce7;color:#166534; }
.shop-closed { background:#fee2e2;color:#991b1b; }
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
<nav class="navbar-menu navbar-fixed-top">
<div class="container">
<div class="logo">
  <a href="index.php"><img src="img/2.png" class="img-responsive"></a>
</div>

<div class="menu text-right">
<ul>
<li><a href="index.php">Home</a></li>
<li><a href="all-menu.php">Drinks</a></li>
<li><a href="review.php">Reviews</a></li>

<li class="user-name">
  <span class="user-icon">👤</span>
  <?= htmlspecialchars($_SESSION['name']) ?>
</li>

<li>
  <a href="/project/line-logout.php" onclick="return confirm('ออกจากระบบใช่ไหม?')">
    Logout
  </a>
</li>
</ul>
</div>
</div>
</nav>
</header>

<!-- ================= ORDER ================= -->
<section class="order">
<div class="container">

<h3>รายละเอียดการสั่งซื้อ</h3>

<table class="tbl-full">
<thead>
<tr>
<th>No.</th>
<th>Name</th>
<th>Price</th>
<th>Temp</th>
<th>Sweet</th>
<th>Qty</th>
<th>Total</th>
<th>Action</th>
</tr>
</thead>

<tbody id="order-items"></tbody>

<tfoot>
<tr>
<th colspan="6">Total</th>
<th id="order-total">0 ฿</th>
<th></th>
</tr>
</tfoot>
</table>

<form class="form" onsubmit="return false;">
<fieldset>
<legend>รายละเอียดการสั่งซื้อ</legend>

<p class="label">ชื่อ-นามสกุล</p>
<input type="text" id="customer_name" required>

<p class="label">เบอร์โทรติดต่อ</p>
<input
  type="tel"
  id="phone"
  name="phone"
  required
  pattern="[0-9]{10}"
  maxlength="10"
  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
  


<fieldset id="payment-section" style="display:none;">
<legend>Payment</legend>

<p class="label">Scan QR Code เพื่อชำระเงิน</p>
<img id="promptpay-qr" class="qr-payment">

<p class="label">อัปโหลดสลิป</p>
<input type="file" id="slip" accept="image/*">
</fieldset>

<input type="button" class="btn-primary" id="confirm-order" value="Confirm Order">

</fieldset>
</form>

</div>
</section>

<!-- ================= SCRIPT ================= -->
<script>
let cart = JSON.parse(localStorage.getItem("cart")) || [];
let totalPrice = 0;

function renderOrder() {
  let html = "";
  totalPrice = 0;

  if (cart.length === 0) {
    $("#order-items").html(`
      <tr><td colspan="8" style="text-align:center">ไม่มีสินค้าในตะกร้า</td></tr>
    `);
    $("#order-total").text("0 ฿");
    $("#payment-section").hide();
    return;
  }

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    totalPrice += itemTotal;

    html += `
      <tr>
        <td>${index + 1}</td>
        <td>${item.name}</td>
        <td>${item.price}</td>
        <td>${item.temp}</td>
        <td>${item.sweet}</td>
        <td>${item.qty}</td>
        <td>${itemTotal}</td>
        <td>
          <button type="button" onclick="removeItem(${index})">ลบ</button>
        </td>
      </tr>
    `;
  });

  $("#order-items").html(html);
  $("#order-total").text(totalPrice + " ฿");

  $("#payment-section").show();
  $("#promptpay-qr").attr("src", "promptpay.php?amount=" + totalPrice);
}

function removeItem(index) {
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderOrder();
}

$("#confirm-order").click(function () {

  if (cart.length === 0) {
    alert("ไม่มีสินค้าในตะกร้า");
    return;
  }

  if (!$("#customer_name").val() || !$("#phone").val()) {
    alert("กรุณากรอกข้อมูลลูกค้า");
    return;
  }

  const slip = $("#slip")[0].files[0];
  if (!slip) {
    alert("กรุณาอัปโหลดสลิป");
    return;
  }

  let formData = new FormData();
  formData.append("customer_name", $("#customer_name").val());
  formData.append("phone", $("#phone").val());
  formData.append("total", totalPrice);
  formData.append("slip", slip);
  formData.append("cart", JSON.stringify(cart));

  $.ajax({
    url: "save-order.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      alert("สั่งซื้อสำเร็จ รอแอดมินตรวจสอบ");
      localStorage.removeItem("cart");
      window.location.href = "index.php";
    }
  });
});

renderOrder();
</script>

</body>
</html>
