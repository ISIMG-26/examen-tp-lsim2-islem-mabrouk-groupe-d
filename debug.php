<?php
session_start();
include 'config.php';

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart Debug Info</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; }
    </style>
</head>
<body>
    <h1>Shopping Cart Debug Information</h1>
    
    <h2 class="info">1. Database Connection</h2>
    <?php
    if ($conn->connect_error) {
        echo '<p class="error">❌ Connection Error: ' . $conn->connect_error . '</p>';
    } else {
        echo '<p class="success">✓ Connected to MySQL</p>';
        echo '<p>Server: ' . $conn->server_info . '</p>';
        echo '<p>Database: ' . $conn->database . '</p>';
    }
    ?>
    
    <h2 class="info">2. Database Tables</h2>
    <?php
    $tables = $conn->query("SHOW TABLES");
    if ($tables) {
        echo '<p class="success">✓ Tables found:</p>';
        echo '<ul>';
        while ($table = $tables->fetch_array()) {
            echo '<li>' . $table[0] . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="error">❌ Could not list tables: ' . $conn->error . '</p>';
    }
    ?>
    
    <h2 class="info">3. Products Table</h2>
    <?php
    $products = $conn->query("SELECT * FROM products");
    if ($products) {
        echo '<p class="success">✓ Products found: ' . $products->num_rows . '</p>';
        if ($products->num_rows > 0) {
            echo '<pre>';
            while ($row = $products->fetch_assoc()) {
                echo print_r($row, true);
            }
            echo '</pre>';
        }
    } else {
        echo '<p class="error">❌ Error: ' . $conn->error . '</p>';
    }
    ?>
    
    <h2 class="info">4. Cart Items (Current Session)</h2>
    <?php
    if (!isset($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = session_id();
    }
    $session_id = $_SESSION['cart_session_id'];
    echo '<p>Session ID: <code>' . $session_id . '</code></p>';
    
    $cart = $conn->query("SELECT c.id, c.product_id, c.quantity, p.name, p.price FROM cart c LEFT JOIN products p ON c.product_id = p.id WHERE c.session_id = '$session_id'");
    if ($cart) {
        echo '<p class="success">✓ Cart items: ' . $cart->num_rows . '</p>';
        if ($cart->num_rows > 0) {
            echo '<pre>';
            while ($row = $cart->fetch_assoc()) {
                echo print_r($row, true);
            }
            echo '</pre>';
        } else {
            echo '<p>Your cart is empty</p>';
        }
    } else {
        echo '<p class="error">❌ Error: ' . $conn->error . '</p>';
    }
    ?>
    
    <h2 class="info">5. Test Add to Cart</h2>
    <form method="POST" action="add_to_cart.php">
        <label>Product ID:</label>
        <input type="number" name="product_id" value="1" min="1">
        <label>Quantity:</label>
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">Test Add to Cart (AJAX)</button>
    </form>
    
    <p>Or test with curl:</p>
    <pre>curl -X POST http://localhost/add_to_cart.php -d "product_id=1&quantity=1"</pre>
    
    <h2 class="info">6. Browser Console</h2>
    <p>Open Developer Tools (F12) and check the Console tab for JavaScript errors.</p>
    
</body>
</html>
