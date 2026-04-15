<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once 'auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = trim($_POST['customer'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');

    if ($customer !== '' && $paymentMethod !== '') {
        $message = "Checkout successful for customer: " . htmlspecialchars($customer) .
                   " using payment method: " . htmlspecialchars($paymentMethod);
    } else {
        $message = "Please enter customer name and payment method.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Checkout</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Checkout Page</h1>
            <p>POS checkout simulation</p>
        </header>

        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
        </nav>

        <main class="card">
            <h2>Checkout Form</h2>

            <form method="post" action="checkout.php">
                <label for="customer">Customer Name</label>
                <input type="text" id="customer" name="customer" placeholder="Enter customer name">

                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method">
                    <option value="">Select payment method</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="PayNow">PayNow</option>
                </select>

                <button type="submit">Complete Checkout</button>
            </form>

            <?php if ($message !== ''): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
        </main>

        <footer class="footer">
            <p>&copy; 2026 POS Simulation Test</p>
        </footer>
    </div>
</body>
</html>