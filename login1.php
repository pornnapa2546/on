<?php
session_start();
include "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();

   if (password_verify($password, $admin['password'])) {
    $_SESSION['admin'] = $admin['username'];
    header("Location: admin-dashboard.php");
    exit;
}
  }

  $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>

  <link rel="shortcut icon" href="img/favicon.ico">
  <link rel="stylesheet" href="css/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="css/hover-min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
  <nav class="navbar-menu navbar-fixed-top">
    <div class="container">
      <div class="logo">
        <a href="index.html">
          <img src="img/2.png" class="img-responsive">
        </a>
      </div>

      <div class="menu text-right">
        <ul>
          <li><a class="hvr-underline-from-center" href="index.php">Home</a></li>
          <li><a class="hvr-underline-from-center" href="all-menu.php">Drinks</a></li>
          <!-- <li><a href="order.html">Order</a></li> -->
          <li><a class="hvr-underline-from-center" href="review.php">Reviews</a></li>
          <li><a class="hvr-underline-from-center" href="login.php">Login</a></li>
          <a id="shopping-cart" class="shopping-cart">
  <i class="fa fa-cart-arrow-down"></i>
  <span class="badge">0</span>
</a>

<div id="cart-content" class="cart-content">
  <h3 class="text-center">Shopping Cart</h3>

  <table class="cart-table" border="0">
    <tr>

      <th>Name</th>
      <th>Price</th>
      <th>Qty</th>
      <th>Total</th>
      <th>Action</th>
    </tr>



    <tr class="cart-total">
      <th colspan="4">Total</th>
      <th class="total-price">0 ฿</th>
      <th></th>
    </tr>
  </table>

  <a href="order.php" class="btn-primary">Confirm Order</a>
</div>
        </ul>
      </div>
    </div>
  </nav>
</header>

<section class="login">
  <div class="container">
    <h2 class="text-center">Admin Login</h2>
    <div class="heading-border"></div>

    <?php if (!empty($error)): ?>
      <p style="color:red; text-align:center;">
        <?= htmlspecialchars($error) ?>
      </p>
    <?php endif; ?>

    <form method="post" class="form">
      <fieldset>
        <legend>Login</legend>

        <p class="label">Username</p>
        <input type="text"
               name="username"
               placeholder="Enter username"
               required>

        <p class="label">Password</p>
        <input type="password"
               name="password"
               placeholder="Enter password"
               required>

        <input type="submit"
               value="Login"
               class="btn-primary">
      </fieldset>
    </form>
  </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="js/custom.js"></script>

</body>
</html>
