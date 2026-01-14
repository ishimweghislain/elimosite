<?php
require_once 'includes/config.php';

// Clear dummy data
$pdo->exec('DELETE FROM blog_posts');
$pdo->exec('DELETE FROM team_members');
$pdo->exec('DELETE FROM properties WHERE id > 5'); // Keep first 5 properties

echo "✅ Dummy data cleared successfully!";
?>
