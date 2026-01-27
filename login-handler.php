<?php
require_once 'includes/config.php';

// Handle login form submissions
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_login'])) {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Log the attempt
    error_log("Login attempt for username: " . $username);
    
    try {
        if (!empty($username) && !empty($password)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?)");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            error_log("User found: " . ($user ? 'yes' : 'no'));
            
            if ($user) {
                error_log("Password verify attempt");
                if (password_verify($password, $user['password'])) {
                    error_log("Password verified successfully");
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => (in_array($user['role'], ['admin', 'user'])) ? ADMIN_URL : 'index.php'
                    ]);
                } else {
                    error_log("Password verification failed");
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid username or password'
                    ]);
                }
            } else {
                error_log("No user found with username: " . $username);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid username or password'
                ]);
            }
        } else {
            error_log("Empty username or password");
            echo json_encode([
                'success' => false,
                'message' => 'Please enter username and password'
            ]);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Login error: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
