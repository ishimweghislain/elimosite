<?php
require_once 'includes/config.php';

// Test database connection
echo "<h2>Database Connection Test</h2>";
if (isset($pdo)) {
    echo "✅ Database connected successfully<br>";
} else {
    echo "❌ Database connection failed<br>";
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
    }
}

// Test login function
echo "<h2>Login Function Test</h2>";
$username = 'admin';
$password = 'admin123';

$stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?)");
$stmt->execute([$username, $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    echo "✅ Login function works<br>";
    echo "User would be redirected to: " . ($user['role'] === 'admin' ? ADMIN_URL : 'index.php') . "<br>";
} else {
    echo "❌ Login function failed<br>";
}

echo "<h2>All Users</h2>";
$stmt = $pdo->query("SELECT username, email, role FROM users");
$users = $stmt->fetchAll();
foreach ($users as $user) {
    echo "Username: " . htmlspecialchars($user['username']) . ", Email: " . htmlspecialchars($user['email']) . ", Role: " . htmlspecialchars($user['role']) . "<br>";
}
?>
