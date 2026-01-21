<?php
include "config/db.php";
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");

while ($row = mysqli_fetch_assoc($result)) {
  echo "<div class='order'>";
  echo "<h3>{$row['customer_name']} - {$row['total']} ฿</h3>";
  echo "<p>Status: <b>{$row['status']}</b></p>";
  echo "<img src='uploads/slips/{$row['slip_image']}' width='150'>";

  if ($row['status'] === 'pending') {
    echo "
      <form method='post' action='update-status.php'>
        <input type='hidden' name='id' value='{$row['id']}'>
        <button name='status' value='approved'>✅ Approve</button>
        <button name='status' value='rejected'>❌ Reject</button>
      </form>
    ";
  }
  echo "</div><hr>";
}
