<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once 'auth.php';


$cartItems = [
    ['item' => 'Coffee', 'qty' => 2, 'price' => 3.50],
    ['item' => 'Sandwich', 'qty' => 1, 'price' => 5.90]
];

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['qty'] * $item['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Cart</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Cart Page</h1>
            <p>POS cart simulation</p>
        </header>

        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
        </nav>

        <main class="card">
            <h2>Cart Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit Price (SGD)</th>
                        <th>Subtotal (SGD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item']); ?></td>
                            <td><?php echo (int)$item['qty']; ?></td>
                            <td><?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo number_format($item['qty'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="total">Total: SGD <?php echo number_format($total, 2); ?></p>
        </main>

        <footer class="footer">
            <p>&copy; 2026 POS Simulation Test</p>
        </footer>
    </div>
</body>
</html>