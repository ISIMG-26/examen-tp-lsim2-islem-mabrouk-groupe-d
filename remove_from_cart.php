<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
        
        if ($cart_id <= 0) {
            $response['message'] = 'Invalid cart item';
            echo json_encode($response);
            exit;
        }
        
        // Get or create session ID
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id();
        }
        $session_id = $_SESSION['cart_session_id'];
        
        // Delete from cart using prepared statement
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $cart_id, $session_id);
        if (!$stmt->execute()) {
            throw new Exception("Delete failed: " . $stmt->error);
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
        $response['message'] = 'Item removed from cart';
        $response['cart_count'] = $cart_data['count'];
        
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
