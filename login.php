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
