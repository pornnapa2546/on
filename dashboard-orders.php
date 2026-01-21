<?php
require "config/db.php";

$view = $_GET['view'] ?? 'today';

$where = "";
$title = "";

if ($view === 'today') {
  $where = "DATE(created_at) = CURDATE()";
  $title = "📦 Orders Today";

} elseif ($view === 'day') {
  $date = $_GET['date'] ?? date('Y-m-d');
  $where = "DATE(created_at) = '$date'";
  $title = "📅 Orders by Day";

} elseif ($view === 'month') {
  $month = $_GET['month'] ?? date('Y-m');
  $where = "DATE_FORMAT(created_at,'%Y-%m') = '$month'";
  $title = "🗓 Orders by Month";

} elseif ($view === 'year') {
  $year = $_GET['year'] ?? date('Y');
  $where = "YEAR(created_at) = '$year'";
  $title = "📆 Orders by Year";
}

$orders = $conn->query("
  SELECT *
  FROM orders
  WHERE $where
  ORDER BY created_at DESC
");
?>

<h2><?= $title ?></h2>

<!-- FILTER -->
<form method="get" style="margin-bottom:15px;">
  <input type="hidden" name="view" value="<?= $view ?>">

  <?php if ($view === 'day'): ?>
    <input type="date" name="date" value="<?= $date ?>">
  <?php elseif ($view === 'month'): ?>
    <input type="month" name="month" value="<?= $month ?>">
  <?php elseif ($view === 'year'): ?>
    <select name="year">
      <?php for ($y=date('Y');$y>=2022;$y--): ?>
        <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option>
      <?php endfor; ?>
    </select>
  <?php endif; ?>

  <?php if ($view !== 'today'): ?>
    <button type="submit">ค้นหา</button>
  <?php endif; ?>
</form>

<table class="data-table">
<thead>
<tr>
  <th>Order No</th>
  <th>Customer</th>
  <th>Total</th>
  <th>Status</th>
  <th>Time</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php while ($o = $orders->fetch_assoc()): ?>
<tr>
  <td><?= $o['order_no'] ?></td>
  <td><?= htmlspecialchars($o['customer_name']) ?></td>
  <td><?= number_format($o['total'],2) ?> ฿</td>
  <td><?= strtoupper($o['status']) ?></td>
  <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
  <td>
    <a class="btn btn-outline"
       href="order-detail.php?id=<?= $o['id'] ?>">
       View
    </a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
