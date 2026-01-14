<?php
require_once 'includes/config.php';

try {
    // Add 'draft' to properties status enum
    $pdo->exec("ALTER TABLE properties MODIFY COLUMN status ENUM('for-rent', 'for-sale', 'under-construction', 'sold', 'rented', 'draft') DEFAULT 'for-rent'");
    echo "Successfully updated properties status column.\n";
} catch (PDOException $e) {
    echo "Error updating properties table: " . $e->getMessage() . "\n";
}
?>
