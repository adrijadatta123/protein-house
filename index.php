<?php
session_start();
require 'dbconnect.php';

// SIMPLE MENU - edit here to change items. Images use full URLs
$menu = [
  ['id'=>1,'name'=>'Grilled Paneer Salad','desc'=>'Mixed greens, grilled paneer, light dressing','category'=>'Salads','price'=>80.00,'image'=>'https://www.saffrontrail.com/wp-content/uploads/2013/07/the-recipe-for-my-best-salad-ever.1024x1024.jpg'],
  ['id'=>2,'name'=>'Protein Shake - Chocolate','desc'=>'Whey protein with milk','category'=>'Shakes','price'=>99.00,'image'=>'https://eatthegains.com/wp-content/uploads/2021/08/Chocolate-Protein-Smoothie-6.jpg'],
  ['id'=>3,'name'=>'Protein Shake - Vanilla','desc'=>'Whey protein with milk','category'=>'Shakes','price'=>99.00,'image'=>'https://cdn.muscleandstrength.com/sites/default/files/field/feature-image/recipe/peanut-butter-banana-shake.jpg'],
  ['id'=>4,'name'=>'Tofu Wrap','desc'=>'Grilled tofu, mixed greens','category'=>'Breakfast','price'=>119.00,'image'=>'https://plantyou.com/wp-content/uploads/2024/02/DSC03122-scaled.jpg'],
  ['id'=>5,'name'=>'Paneer Tikka Bowl','desc'=>'Grilled paneer with salad & chutney','category'=>'Mains','price'=>189.00,'image'=>'https://www.honeywhatscooking.com/wp-content/uploads/2025/06/Paneer-Tikka-Rice-Bowls-featured.jpg'],
  ['id'=>6,'name'=>'Tuna Sandwich','desc'=>'Whole wheat bread, tuna & veggies','category'=>'Sandwiches','price'=>139.00,'image'=>'https://cravinghomecooked.com/wp-content/uploads/2023/11/tuna-salad-sandwich-1-16.jpg'],
  ['id'=>7,'name'=>'Protein Pancakes','desc'=>'Oats & protein pancakes with honey','category'=>'Breakfast','price'=>159.00,'image'=>'https://cdn.loveandlemons.com/wp-content/uploads/2025/09/protein-pancakes.jpg'],
  ['id'=>8,'name'=>'Quinoa Salad','desc'=>'Quinoa, cucumber, tomato, lemon','category'=>'Salads','price'=>139.00,'image'=>'https://cookieandkate.com/images/2017/08/best-quinoa-salad-recipe-3.jpg'],
  ['id'=>9,'name'=>'Veggie Wrap','desc'=>'Grilled veggies & hummus in tortilla','category'=>'Wraps','price'=>119.00,'image'=>'https://foodwithfeeling.com/wp-content/uploads/2021/04/vegan-wraps-9.jpg'],
  ['id'=>10,'name'=>'Fruit Bowl','desc'=>'Seasonal fruits bowl','category'=>'Sides','price'=>89.00,'image'=>'https://images.archanaskitchen.com/images/recipes/world-recipes/healthy-breakfast-recipes/breakfast-bowl-recipes/fresh_fruit_bowl_recipe_d16b4666b8.jpg'],
];

// handle add-to-cart POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $menu_id = intval($_POST['menu_id']);
    $qty = max(1, intval($_POST['quantity'] ?? 1));

    // find item in menu
    $found = null;
    foreach ($menu as $m) if ($m['id'] === $menu_id) { $found = $m; break; }

    if ($found) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $exists = false;
        foreach ($_SESSION['cart'] as &$c) {
            if ($c['id'] === $found['id']) {
                $c['qty'] += $qty;
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $_SESSION['cart'][] = ['id'=>$found['id'],'name'=>$found['name'],'price'=>$found['price'],'qty'=>$qty];
        }
    }
    header('Location: index.php#menu'); 
    exit;
}

// cart summary
$cart_count = 0; 
$cart_total = 0.0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c) { 
        $cart_count += $c['qty']; 
        $cart_total += $c['price']*$c['qty']; 
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Protein House — Healthy Eats</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="hero">
    <div class="hero-inner">
      <h1>Protein House</h1>
      <p>Healthy. Protein-rich. Delicious.</p>
      <a class="cta" href="#menu">See Menu</a>
    </div>
    <div class="hero-photo">
      <img src="https://images.unsplash.com/photo-1604908177443-0303d1fd0f5a" alt="Healthy Sandwich">
    </div>
  </header>

  <main class="container">
    <section id="menu">
      <h2>Our Healthy Menu</h2>
      <div class="grid">
        <?php foreach ($menu as $item): ?>
          <div class="card">
            <!-- FIXED IMAGE LINE BELOW -->
            <img src="<?=htmlspecialchars($item['image'])?>" alt="<?=htmlspecialchars($item['name'])?>">
            <div class="card-body">
              <h3><?=htmlspecialchars($item['name'])?></h3>
              <p class="muted"><?=htmlspecialchars($item['desc'])?></p>
              <div class="row-between">
                <div class="price">₹<?=number_format($item['price'],2)?></div>
                <form method="post" class="inline-form">
                  <input type="hidden" name="menu_id" value="<?=intval($item['id'])?>">
                  <input type="number" name="quantity" value="1" min="1" class="qty">
                  <button name="add_to_cart" class="btn">Add to Cart</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="support">
      <h2>Customer Support</h2>
      <p>Need help with your order? Call us or send a message — we're here to help.</p>
      <div class="support-cards">
        <div class="support-card">
          <strong>Phone</strong>
          <p><a href="tel:+91 7439938815">+91 7439938815</a></p>
        </div>
        <div class="support-card">
          <strong>Email</strong>
          <p><a href="mailto:support@proteinhouse.example">support@proteinhouse.example</a></p>
        </div>
        <div class="support-card">
          <strong>Live Chat</strong>
          <p><a href="mailto:support@proteinhouse.example?subject=Chat%20request">Open quick chat</a></p>
        </div>
      </div>
    </section>
  </main>

  <!-- Sticky bottom cart bar -->
  <div class="cart-bar">
    <div class="cart-left">
      <span class="cart-icon">🛒</span>
      <div>
        <div class="small">Your Cart</div>
        <div class="bold"><?=intval($cart_count)?> items • ₹<?=number_format($cart_total,2)?></div>
      </div>
    </div>
    <div class="cart-right">
      <a href="cart.php" class="btn btn-light">View Cart</a>
      <a href="checkout.php" class="btn">Checkout</a>
    </div>
  </div>

  <footer class="footer">
    <p>&copy; <?=date('Y')?> Protein House — Healthy food for a strong body.</p>
  </footer>
</body>
</html>
