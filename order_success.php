<?php
require 'dbconnect.php';
$order_id = intval($_GET['order_id'] ?? 0);
$order = null; $items = [];
if ($order_id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if ($order) {
        $stmt2 = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt2->execute([$order_id]);
        $items = $stmt2->fetchAll();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Order Confirmed</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="mini-header"><a href="index.php" class="link-back">← Back to menu</a><h1>Order Confirmed</h1></header>
<main class="container">
  <?php if (!$order): ?>
    <p class="muted">Order not found.</p>
  <?php else: ?>
    <div class="confirmation">
      <h2>Thanks, <?=htmlspecialchars($order['customer_name'])?> — your order is received ✅</h2>
      <p><strong>Order #</strong> <?=intval($order['id'])?> &nbsp;•&nbsp; <strong>Total:</strong> ₹<?=number_format($order['total'],2)?></p>
      <h3>Items</h3>
      <ul>
        <?php foreach ($items as $it): ?>
          <li><?=htmlspecialchars($it['item_name'])?> × <?=intval($it['quantity'])?> — ₹<?=number_format($it['price']*$it['quantity'],2)?></li>
        <?php endforeach; ?>
      </ul>

      <p>We will contact you at <strong><?=htmlspecialchars($order['customer_phone'])?></strong>. If you need immediate help, call <a href="tel:+911234567890">+91 12345 67890</a>.</p>
      <a href="index.php" class="btn">Back to Menu</a>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
