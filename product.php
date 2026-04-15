<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once 'auth.php';


$products = [
    ['id' => 1, 'name' => 'Coffee', 'price' => 3.50],
    ['id' => 2, 'name' => 'Tea', 'price' => 2.80],
    ['id' => 3, 'name' => 'Sandwich', 'price' => 5.90],
    ['id' => 4, 'name' => 'Cake Slice', 'price' => 4.20]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Products</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Product Page</h1>
            <p>POS product simulation</p>
        </header>

        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
        </nav>

        <main class="card">
            <h2>Available Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item</th>
                        <th>Price (SGD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string)$product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo number_format($product['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>

        <footer class="footer">
            <p>&copy; 2026 POS Simulation Test</p>
        </footer>
    </div>
</body>
</html>