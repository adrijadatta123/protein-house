<?php
session_start();
require 'dbconnect.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment = $_POST['payment_method'] ?? 'COD';

    if ($name === '' || $phone === '') {
        $error = 'Please enter name and phone.';
    } else {
        // calculate total
        $total = 0;
        foreach ($cart as $c) $total += $c['price'] * $c['qty'];

        // insert order and items with transaction
        try {
            $pdo->beginTransaction();
            // ✅ removed support_note
            $ins = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, payment_method, total) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$name, $phone, $address, $payment, $total]);
            $order_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $c) {
                $stmt->execute([$order_id, $c['name'], $c['qty'], $c['price']]);
            }

            $pdo->commit();
            // clear cart and redirect to success
            unset($_SESSION['cart']);
            header('Location: order_success.php?order_id=' . $order_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to save order: ' . $e->getMessage();
        }
    }
}

$total = 0;
foreach ($cart as $c) $total += $c['price'] * $c['qty'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Checkout</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="mini-header">
  <a href="cart.php" class="link-back">← Back to cart</a>
  <h1>Checkout</h1>
</header>

<main class="container">
  <?php if ($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
  <div class="checkout-grid">
    <div class="checkout-left">
      <h3>Delivery Details</h3>
      <form method="post">
        <label>Full name
          <input type="text" name="name" required>
        </label>
        <label>Phone
          <input type="text" name="phone" required>
        </label>
        <label>Address
          <textarea name="address" rows="3"></textarea>
        </label>
        <label>Payment method
          <select name="payment_method">
            <option value="COD">Cash on Delivery (COD)</option>
            <option value="UPI">UPI (Scan & Pay)</option>
            <option value="Card">Card (Pay on delivery / simulated)</option>
          </select>
        </label>
        <button name="place_order" class="btn">Place Order</button>
      </form>
    </div>

    <aside class="checkout-right">
      <h3>Order Summary</h3>
      <ul class="order-list">
        <?php foreach ($cart as $c): ?>
          <li><?= htmlspecialchars($c['name']) ?> × <?= intval($c['qty']) ?> <span>₹<?= number_format($c['price'] * $c['qty'], 2) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <div class="summary-total">Total: ₹<?= number_format($total, 2) ?></div>
      <div class="support-small">
        <strong>Need help?</strong>
        <p>Call <a href="tel:+911234567890">+91 12345 67890</a> or
           <a href="mailto:support@proteinhouse.example">email us</a>.
        </p>
      </div>
    </aside>
  </div>
</main>
</body>
</html>

