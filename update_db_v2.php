<?php
require_once 'includes/config.php';

try {
    // Add images column to blog_posts if it doesn't exist
    $stmt = $pdo->prepare("SHOW COLUMNS FROM blog_posts LIKE 'images'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE blog_posts ADD COLUMN images JSON AFTER image");
        echo "Added 'images' column to 'blog_posts' table.<br>";
    } else {
        echo "'images' column already exists in 'blog_posts' table.<br>";
    }

    // Ensure properties has images column
    $stmt = $pdo->prepare("SHOW COLUMNS FROM properties LIKE 'images'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE properties ADD COLUMN images JSON AFTER image_main");
        echo "Added 'images' column to 'properties' table.<br>";
    } else {
        echo "'images' column already exists in 'properties' table.<br>";
    }

    echo "Database migration completed successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
