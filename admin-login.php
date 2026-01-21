<form method="post">
  <h2>Admin Login</h2>
  <input name="username" placeholder="Username" required>
  <input name="password" type="password" placeholder="Password" required>
  <button>Login</button>
</form>

<?php
session_start();
include "config/db.php";

if ($_POST) {
  $u = $_POST['username'];
  $p = md5($_POST['password']);

  $sql = "SELECT * FROM admins WHERE username='$u' AND password='$p'";
  $res = mysqli_query($conn, $sql);

  if (mysqli_num_rows($res) === 1) {
    $_SESSION['admin'] = true;
    header("Location: admin.php");
  } else {
    echo "Login failed";
  }
}
?>
