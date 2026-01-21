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

    <style>
      .shop-status {
        margin: 15px auto;
        padding: 12px;
        max-width: 400px;
        text-align: center;
        border-radius: 8px;
        font-weight: 500;
      }
      .shop-open {
        background: #dcfce7;
        color: #166534;
      }
      .shop-closed {
        background: #fee2e2;
        color: #991b1b;
      }
      .btn-disabled {
        background: #ccc !important;
        cursor: not-allowed !important;
      }
      .drink-options {
  margin-top: 10px;
}

.option-group {
  margin-bottom: 14px;
}

.option-label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

.option-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.option-buttons button {
  padding: 8px 16px;
  border-radius: 20px;
  border: 1.5px solid #ddd;
  background: #fff;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.option-buttons button:hover {
  background: #f3f3f3;
}

.option-buttons button.active {
  background: #ff7a18;
  color: #fff;
  border-color: #ff7a18;
  box-shadow: 0 4px 10px rgba(255, 122, 24, 0.3);
}


    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <nav id="site-top-nav" class="navbar-menu navbar-fixed-top">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="img/2.png" class="img-responsive">
                </a>
            </div>

            <div class="menu text-right">
                <ul>
                    <li><a class="hvr-underline-from-center" href="index.php">Home</a></li>
                    <li><a class="hvr-underline-from-center" href="all-menu.php">Drinks</a></li>
                    <li><a class="hvr-underline-from-center" href="review.php">Reviews</a></li>

                    <!-- ===== LOGIN / LOGOUT ===== -->
                    <?php if (!empty($_SESSION['line_user_id'])): ?>
                        <li class="user-name">
    <span class="user-icon">👤</span>
    <?= htmlspecialchars($_SESSION['name']) ?>
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

                    <!-- ===== CART ===== -->
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


<!-- ================= CATEGORY ================= -->
<section class="categories">
    <div class="container">
        <h2 class="text-center">ประเภท (Explore Drinks)</h2>
        <div class="heading-border"></div>
<div class="shop-status <?= $shopOpen ? 'shop-open' : 'shop-closed' ?>">
<?= $shopOpen ? "🟢 ร้านเปิด รับออเดอร์ได้" : "🔴 ร้านปิด ไม่รับออเดอร์ในขณะนี้" ?>
</div>
        <div class="grid-3">
            <a href="coffee.php">
                <div class="float-container">
                    <img src="img/category/กาแฟฟ.png" class="img-responsive">
                    <h3 class="float-text text-white">กาแฟ (Coffee)</h3>
                </div>
            </a>
            <a href="tea.php">
                <div class="float-container">
                    <img src="img/category/ชา.png" class="img-responsive">
                    <h3 class="float-text text-white">ชา (Tea)</h3>
                </div>
            </a>
            <a href="milk.php">
                <div class="float-container">
                    <img src="img/category/นม.png" class="img-responsive">
                    <h3 class="float-text text-white">นม (Milk)</h3>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- ================= RECOMMEND MENU ================= -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center text-white">เมนูแนะนำ (Recommend)</h2>
        <div class="heading-border"></div>

        <div class="grid-2">
        <?php
        $rec = $conn->query("
            SELECT * FROM menus
            WHERE is_recommend = 1
            ORDER BY created_at DESC
        ");

        while ($m = $rec->fetch_assoc()):
        ?>
            <div class="food-menu-box">
                <div class="food-menu-img">
                    <img src="<?= htmlspecialchars($m['image']) ?>"
                         class="img-responsive img-curve">
                </div>

                <div class="food-menu-desc">
                    <h4><?= htmlspecialchars($m['name']) ?></h4>
                    <p class="food-price">
                        <?= number_format($m['price'], 0) ?> ฿
                    </p>
<div class="drink-options">

  <!-- TEMPERATURE -->
  <div class="option-group">
    <label class="option-label">Temp</label>
    <div class="option-buttons temp-options">
      <button type="button" data-value="hot">🔥 Hot</button>
      <button type="button" data-value="cold">🧊 Cold</button>
    </div>
    <input type="hidden" class="drink-temp" value="">
  </div>

  <!-- SWEET LEVEL -->
  <div class="option-group">
    <label class="option-label">Sweet</label>
    <div class="option-buttons sweet-options">
      <button type="button" data-value="0%">0%</button>
      <button type="button" data-value="25%">25%</button>
      <button type="button" data-value="50%">50%</button>
      <button type="button" data-value="75%">75%</button>
      <button type="button" data-value="100%">100%</button>
    </div>
    <input type="hidden" class="drink-sweet" value="">
  </div>

</div>


<input type="number" class="qty" value="1" min="1" <?= !$shopOpen ? 'disabled' : '' ?>>
                    <input type="button"
                           class="btn-primary btn-add <?= !$shopOpen ? 'btn-disabled' : '' ?>"
                           value="<?= $shopOpen ? 'Add To Cart' : 'ไม่สามารถเพิ่มได้' ?>"
                           <?= $shopOpen ? "onclick=\"addToCart('".htmlspecialchars($m['name'],ENT_QUOTES)."',{$m['price']})\"" : 'disabled' ?>>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="js/custom.js"></script>
<script>
document.querySelectorAll(".food-menu-box").forEach(box => {

  // TEMP
  box.querySelectorAll(".temp-options button").forEach(btn => {
    btn.addEventListener("click", () => {
      box.querySelectorAll(".temp-options button")
         .forEach(b => b.classList.remove("active"));

      btn.classList.add("active");
      box.querySelector(".drink-temp").value = btn.dataset.value;
    });
  });

  // SWEET
  box.querySelectorAll(".sweet-options button").forEach(btn => {
    btn.addEventListener("click", () => {
      box.querySelectorAll(".sweet-options button")
         .forEach(b => b.classList.remove("active"));

      btn.classList.add("active");
      box.querySelector(".drink-sweet").value = btn.dataset.value;
    });
  });

});
</script>
</body>
</html>
