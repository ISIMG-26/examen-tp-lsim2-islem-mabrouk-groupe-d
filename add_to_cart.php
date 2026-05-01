<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Ensure tables exist
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        session_id VARCHAR(255)
    )");

    // Add sample products if they don't exist
    $check = $conn->query("SELECT id FROM products LIMIT 1");
    if ($check && $check->num_rows === 0) {
        $insert = $conn->query("INSERT INTO products (name, price, image, description) VALUES
        ('Sport Shoes For Men', 99.00, 'images/single-product-thumb1.jpg', 'Comfortable sport shoes for men'),
        ('Brand Shoes For Men', 99.00, 'images/single-product-thumb2.jpg', 'Stylish brand shoes for men'),
        ('Running Shoes For Men', 99.00, 'images/single-product-thumb1.jpg', 'Premium running shoes for men'),
        ('Women Casual Shoes', 89.00, 'images/single-product-thumb2.jpg', 'Comfortable casual shoes for women')");
        
        if (!$insert) {
            error_log("Failed to insert products: " . $conn->error);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        error_log("Cart Request - Product ID: $product_id, Quantity: $quantity");
        
        if ($product_id <= 0 || $quantity <= 0) {
            $response['message'] = 'Invalid product ID or quantity';
            echo json_encode($response);
            exit;
        }
        
        // Get or create session ID
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
        }
        $session_id = $_SESSION['cart_session_id'];
        error_log("Session ID: $session_id");
        
        // Check if product exists using prepared statement
        $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("Product not found: $product_id");
            $response['message'] = 'Product not found with ID: ' . $product_id;
            echo json_encode($response);
            exit;
        }
        
        // Check if product already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE product_id = ? AND session_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $product_id, $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update quantity
            error_log("Updating cart item");
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ii", $new_quantity, $row['id']);
            if (!$stmt->execute()) {
                throw new Exception("Update failed: " . $stmt->error);
            }
        } else {
            // Insert new cart item
            error_log("Inserting new cart item");
            $stmt = $conn->prepare("INSERT INTO cart (product_id, quantity, session_id) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iis", $product_id, $quantity, $session_id);
            if (!$stmt->execute()) {
                throw new Exception("Insert failed: " . $stmt->error);
            }
        }
        
        // Get cart count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_data = $result->fetch_assoc();
        
        $response['success'] = true;
        $response['message'] = 'Product added to cart successfully';
        $response['cart_count'] = $cart_data['count'];
        
        error_log("Success: " . json_encode($response));
        echo json_encode($response);
    } else {
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Server Error: ' . $e->getMessage();
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode($response);
}
?>
