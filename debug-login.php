<?php
require_once 'includes/config.php';

// Test basic database connection
echo "<h2>Database Connection Test</h2>";
if (isset($pdo)) {
    echo "✅ Database connected successfully<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test if admin user exists
echo "<h2>Admin User Test</h2>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "✅ Admin user found<br>";
    echo "Username: " . htmlspecialchars($admin['username']) . "<br>";
    echo "Email: " . htmlspecialchars($admin['email']) . "<br>";
    echo "Role: " . htmlspecialchars($admin['role']) . "<br>";
    
    // Test password verification
    if (password_verify('admin123', $admin['password'])) {
        echo "✅ Password verification works<br>";
    } else {
        echo "❌ Password verification failed<br>";
        echo "Stored hash: " . htmlspecialchars($admin['password']) . "<br>";
    }
} else {
    echo "❌ Admin user not found<br>";
    
    // Create admin user if not exists
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute(['admin', 'admin@elimo.rw', $hashed_password, 'Administrator', 'admin'])) {
        echo "✅ Admin user created successfully<br>";
    } else {
        echo "❌ Failed to create admin user<br>";
        print_r($stmt->errorInfo());
    }
}

// Test AJAX login simulation
echo "<h2>AJAX Login Test</h2>";
$username = 'admin';
$password = 'admin123';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?)");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        echo "✅ AJAX login simulation works<br>";
        echo "User would be redirected to: " . ($user['role'] === 'admin' ? ADMIN_URL : 'index.php') . "<br>";
    } else {
        echo "❌ AJAX login simulation failed<br>";
    }
} catch (PDOException $e) {
    echo "❌ Database error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Check error reporting
echo "<h2>PHP Error Reporting</h2>";
echo "Error reporting: " . (error_reporting() ? 'ON' : 'OFF') . "<br>";
echo "Display errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";

// Check session
echo "<h2>Session Status</h2>";
echo "Session status: " . session_status() . "<br>";
echo "Session ID: " . session_id() . "<br>";
?>
