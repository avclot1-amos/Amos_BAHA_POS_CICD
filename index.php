<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once 'auth.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple POS Simulation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>BAHA POS Simulation</h1>
            <p>CI/CD test page for Jenkins pipeline</p>
        </header>

        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
        </nav>

        <main class="card">
            <h2>System Status</h2>
            <ul>
                <li>Web server: <strong>Online</strong></li>
                <li>Authentication: <strong>Enabled</strong></li>
                <li>POS simulation mode: <strong>Active</strong></li>
            </ul>

            <button id="statusBtn">Run Frontend Check</button>
            <p id="statusMessage"></p>
        </main>

        <footer class="footer">
            <p>&copy; 2026 POS Simulation Test</p>
        </footer>
    </div>

    <script src="assets/app.js"></script>
</body>
</html>