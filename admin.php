<?php
include "config/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Orders</title>
  <style>
    body {
      font-family: Arial;
      background: #f5f7fa;
      padding: 20px;
    }
    .order {
      background: #fff;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    img {
      margin-top: 10px;
      border-radius: 6px;
      max-width: 200px;
    }
  </style>
</head>
<body>

<h1>📦 Order List</h1>

<?php
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");

while ($row = mysqli_fetch_assoc($result)) {
  echo "<div class='order'>";
  echo "<h3>{$row['customer_name']} - {$row['total']} ฿</h3>";
  echo "<p>Phone: {$row['phone']}</p>";
  echo "<p>Address: {$row['address']}</p>";
  echo "<img src='uploads/slips/{$row['slip_image']}'>";
  echo "</div>";
}
?>

</body>
</html>
