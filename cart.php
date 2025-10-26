<?php
session_start();

// update quantities or remove
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $index => $q) {
            $q = max(0, intval($q));
            if ($q === 0) {
                unset($_SESSION['cart'][$index]);
            } else {
                $_SESSION['cart'][$index]['qty'] = $q;
            }
        }
        // reindex
        if (!empty($_SESSION['cart'])) $_SESSION['cart'] = array_values($_SESSION['cart']);
    } elseif (isset($_POST['clear'])) {
        unset($_SESSION['cart']);
    }
    header('Location: cart.php'); exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0.0;
foreach ($cart as $c) $total += $c['price'] * $c['qty'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Your Cart</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="mini-header"><a href="index.php" class="link-back">← Back to menu</a><h1>Your Cart</h1></header>
<main class="container">
  <?php if (empty($cart)): ?>
    <p class="muted">Your cart is empty. Add delicious healthy items from the menu.</p>
  <?php else: ?>
    <form method="post">
      <table class="cart-table">
        <thead>
          <tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $i=>$c): $sub = $c['price']*$c['qty']; ?>
            <tr>
              <td><?=htmlspecialchars($c['name'])?></td>
              <td>₹<?=number_format($c['price'],2)?></td>
              <td><input type="number" name="qty[<?=$i?>]" value="<?=intval($c['qty'])?>" min="0" class="qty-input"></td>
              <td>₹<?=number_format($sub,2)?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><td colspan="3" class="text-right"><strong>Total</strong></td><td><strong>₹<?=number_format($total,2)?></strong></td></tr>
        </tfoot>
      </table>

      <div class="cart-actions">
        <button type="submit" name="update" class="btn">Update Cart</button>
        <button type="submit" name="clear" class="btn btn-light" onclick="return confirm('Clear cart?')">Clear Cart</button>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
      </div>
    </form>
  <?php endif; ?>
</main>
</body>
</html>
