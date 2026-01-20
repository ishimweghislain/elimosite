<?php
require_once 'includes/config.php';

try {
    echo "Updating properties table...\n";
    $pdo->exec("ALTER TABLE properties ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL;");
    $pdo->exec("ALTER TABLE properties ADD COLUMN instagram_url VARCHAR(255) DEFAULT NULL;");
    $pdo->exec("ALTER TABLE properties ADD COLUMN video VARCHAR(255) DEFAULT NULL;");
    echo "Properties table updated.\n";
} catch (PDOException $e) {
    echo "Notice: Column may already exist or error in properties table: " . $e->getMessage() . "\n";
}

try {
    echo "Updating blog_posts table...\n";
    $pdo->exec("ALTER TABLE blog_posts ADD COLUMN video VARCHAR(255) DEFAULT NULL;");
    echo "Blog_posts table updated.\n";
} catch (PDOException $e) {
    echo "Notice: Column may already exist or error in blog_posts table: " . $e->getMessage() . "\n";
}
?>
