<?php
require_once 'includes/config.php';

echo "<h2>Creating/Verifying Admin User</h2>";

// First, delete any existing admin user to avoid conflicts
$stmt = $pdo->prepare("DELETE FROM users WHERE username = 'admin'");
$stmt->execute();
echo "✅ Cleared any existing admin user<br>";

// Create new admin user with known password
$username = 'admin';
$email = 'admin@elimo.rw';
$password = 'admin123';
$full_name = 'Administrator';
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "✅ Password hashed: " . htmlspecialchars($hashed_password) . "<br>";

// Insert the admin user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
$result = $stmt->execute([$username, $email, $hashed_password, $full_name, $role]);

if ($result) {
    echo "✅ Admin user created successfully<br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br>";
    echo "Role: " . htmlspecialchars($role) . "<br>";
} else {
    echo "❌ Failed to create admin user<br>";
    print_r($stmt->errorInfo());
    exit;
}

// Test the login
echo "<h2>Testing Login</h2>";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    echo "✅ User found in database<br>";
    echo "Stored hash: " . htmlspecialchars($user['password']) . "<br>";
    
    if (password_verify($password, $user['password'])) {
        echo "✅ Password verification successful<br>";
        echo "🎉 Login should work now!<br>";
    } else {
        echo "❌ Password verification failed<br>";
    }
} else {
    echo "❌ User not found in database<br>";
}

// Show all users for debugging
echo "<h2>All Users in Database</h2>";
$stmt = $pdo->query("SELECT id, username, email, role FROM users");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo "ID: " . $user['id'] . ", Username: " . htmlspecialchars($user['username']) . 
         ", Email: " . htmlspecialchars($user['email']) . ", Role: " . htmlspecialchars($user['role']) . "<br>";
}

echo "<h2>Next Steps</h2>";
echo "1. Go to your website and try logging in with:<br>";
echo "   Username: <strong>admin</strong><br>";
echo "   Password: <strong>admin123</strong><br>";
echo "2. If it still doesn't work, check the browser console for errors<br>";
echo "3. You can delete this file after testing<br>";
?>
