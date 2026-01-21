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



/* =====================
   SUBMIT REVIEW
===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name    = $_POST['name'];
  $comment = $_POST['comment'];
  $rating  = (int)$_POST['rating'];

  $imageName = null;
  if (!empty($_FILES['image']['name'])) {
    $imageName = time().'_'.$_FILES['image']['name'];
    move_uploaded_file(
      $_FILES['image']['tmp_name'],
      "uploads/reviews/".$imageName
    );
  }

  $stmt = $conn->prepare("
    INSERT INTO reviews (name, comment, rating, image)
    VALUES (?, ?, ?, ?)
  ");
  $stmt->bind_param("ssis", $name, $comment, $rating, $imageName);
  $stmt->execute();
}

/* =====================
   FETCH DATA
===================== */
$avg = $conn->query("SELECT AVG(rating) avg_rating FROM reviews")
            ->fetch_assoc()['avg_rating'] ?? 0;

$reviews = $conn->query("
  SELECT * FROM reviews
  ORDER BY created_at DESC
  LIMIT 5
");
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

<style>
.review-box {
  background:#fff;
  padding:20px;
  border-radius:10px;
  box-shadow:0 2px 8px rgba(0,0,0,.1);
  margin-bottom:30px;
}
.rating-emoji label {
  font-size:32px;
  cursor:pointer;
  margin-right:8px;
}
.rating-emoji input {
  display:none;
}
.review-item {
  border-bottom:1px solid #eee;
  padding:15px 0;
}
.review-item img {
  width:80px;
  border-radius:6px;
  margin-top:10px;
}
.avg-rating {
  font-size:28px;
  color:#f39c12;
  font-weight:bold;
}
.star-rating {
  direction: rtl; /* เรียงดาวจากขวาไปซ้าย */
  display: inline-flex;
}

.star-rating input {
  display: none;
}

.star-rating label {
  font-size: 36px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s;
}

/* เวลา hover */
.star-rating label:hover,
.star-rating label:hover ~ label {
  color: #f5b301;
}

/* เวลาคลิกเลือกแล้ว */
.star-rating input:checked ~ label {
  color: #f5b301;
}

</style>
</head>

<body>


<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <nav id="site-top-nav" class="navbar-menu navbar-fixed-top">
        <div class="container navbar-flex">

            <!-- LOGO -->
            <div class="logo">
                <a href="index.php">
                    <img src="img/2.png" class="img-responsive">
                </a>
            </div>

            <!-- MENU RIGHT -->
            <div class="menu">
                <ul class="nav-right">
                    <li><a class="hvr-underline-from-center" href="index.php">Home</a></li>
                    <li><a class="hvr-underline-from-center" href="all-menu.php">Drinks</a></li>
                    <li><a class="hvr-underline-from-center" href="review.php">Reviews</a></li>

                    <!-- LOGIN / LOGOUT -->
                    <?php if (!empty($_SESSION['line_user_id'])): ?>
                        <li class="nav-user">
                            <span class="user-icon">👤</span>
                            <span class="user-name-text">
                                <?= htmlspecialchars($_SESSION['name']) ?>
                            </span>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center"
                               href="/project/line-logout.php"
                               onclick="return confirm('ออกจากระบบใช่ไหม?')">
                               Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="hvr-underline-from-center"
                               href="/project/line/line-login.php">
                               Login with LINE
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- CART -->
                     <li class="cart-wrapper">
                        <a id="shopping-cart" class="shopping-cart <?= !$shopOpen ? 'btn-disabled' : '' ?>">
                            <i class="fa fa-cart-arrow-down"></i>
                            <span class="badge">0</span>
                        </a>

                        <?php if ($shopOpen): ?>
                        <div id="cart-content" class="cart-content">
                            <h3 class="text-center">Shopping Cart</h3>

                            <table class="cart-table">
                               <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Temp</th>
                                    <th>Sweet</th>                                    
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>

                                <tr class="cart-total">
                                    <th colspan="6">Total</th>
                                    <th class="total-price">0 ฿</th>
                                </tr>
                            </table>

                            <a href="order.php" class="btn-primary">
                                Confirm Order
                            </a>
                        </div>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</header>

<!-- ================= REVIEW SECTION ================= -->
<section class="categories">
<div class="container">

<h2 class="text-center">⭐ รีวิวร้านค้า</h2>
<div class="heading-border"></div>

<p class="text-center">
คะแนนเฉลี่ยร้าน:
<span class="avg-rating">
<?= number_format($avg,1) ?>/5 ⭐
</span>
</p>

<!-- ===== FORM ===== -->
<div class="review-box">
<form method="post" enctype="multipart/form-data">

<p class="label">ชื่อของคุณ</p>
<input type="text" name="name" required>

<p class="label">ระดับความพึงพอใจ</p>

<div class="star-rating">
  <input type="radio" name="rating" id="star5" value="5" required>
  <label for="star5">★</label>

  <input type="radio" name="rating" id="star4" value="4">
  <label for="star4">★</label>

  <input type="radio" name="rating" id="star3" value="3">
  <label for="star3">★</label>

  <input type="radio" name="rating" id="star2" value="2">
  <label for="star2">★</label>

  <input type="radio" name="rating" id="star1" value="1">
  <label for="star1">★</label>
</div>


<p class="label">ความคิดเห็น</p>
<textarea name="comment" rows="4" required></textarea>

<p class="label">แนบรูปรีวิว (ถ้ามี)</p>
<input type="file" name="image" accept="image/*">

<br><br>
<input type="submit" value="ส่งรีวิว" class="btn-primary">

</form>
</div>



<?php while ($r = $reviews->fetch_assoc()): ?>
<div class="review-item">
<strong><?= htmlspecialchars($r['name']) ?></strong><br>
<?= str_repeat("⭐", $r['rating']) ?>
<p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>

<?php if ($r['image']): ?>
<img src="uploads/reviews/<?= $r['image'] ?>">
<?php endif; ?>

<br>
<small><?= date("d/m/Y H:i", strtotime($r['created_at'])) ?></small>
</div>
<?php endwhile; ?>

</div>
</section>
<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

<!-- CART SCRIPT (สำคัญมาก) -->
<script src="js/custom.js"></script>

</body>
</html>
