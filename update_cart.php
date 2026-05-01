<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        if ($cart_id <= 0 || $quantity < 0) {
            $response['message'] = 'Invalid cart item or quantity';
            echo json_encode($response);
            exit;
        }
        
        // Get or create session ID
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
        }
        $session_id = $_SESSION['cart_session_id'];
        
        if ($quantity === 0) {
            // Delete if quantity is 0
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("is", $cart_id, $session_id);
            if (!$stmt->execute()) {
                throw new Exception("Delete failed: " . $stmt->error);
            }
        } else {
            // Update quantity
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iis", $quantity, $cart_id, $session_id);
            if (!$stmt->execute()) {
                throw new Exception("Update failed: " . $stmt->error);
            }
        }
        
        // Recalculate totals using prepared statement
        $stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) as subtotal
                FROM cart c
                LEFT JOIN products p ON c.product_id = p.id
                WHERE c.session_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $totals = $result->fetch_assoc();
        
        $subtotal = $totals['subtotal'] ?? 0;
        $tax = $subtotal * 0.08;
        $total = $subtotal + $tax;
        
        $response['success'] = true;
        $response['message'] = 'Cart updated';
        $response['subtotal'] = number_format($subtotal, 2);
        $response['tax'] = number_format($tax, 2);
        $response['total'] = number_format($total, 2);
        
        echo json_encode($response);
    } else {
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Server Error: ' . $e->getMessage();
    http_response_code(500);
    echo json_encode($response);
}
?>
