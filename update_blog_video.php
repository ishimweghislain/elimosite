<?php
require_once 'includes/config.php';

try {
    // Add youtube_url column to blog_posts if it doesn't exist
    $stmt = $pdo->prepare("SHOW COLUMNS FROM blog_posts LIKE 'youtube_url'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE blog_posts ADD COLUMN youtube_url VARCHAR(255) AFTER video");
        echo "Added 'youtube_url' column to 'blog_posts' table.<br>";
    } else {
        echo "'youtube_url' column already exists in 'blog_posts' table.<br>";
    }

    echo "Database migration completed successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
