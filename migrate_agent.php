<?php
require_once 'includes/config.php';

try {
    global $pdo;
    
    // Add agent_id to properties
    $pdo->exec("ALTER TABLE properties ADD COLUMN agent_id INT DEFAULT NULL AFTER development_id");
    echo "Added agent_id to properties table.<br>";
    
    // Add agent_id to developments
    $pdo->exec("ALTER TABLE developments ADD COLUMN agent_id INT DEFAULT NULL AFTER id");
    echo "Added agent_id to developments table.<br>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
