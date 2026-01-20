<?php
require_once 'includes/config.php';
echo "--- PROPERTIES ---\n";
$res = $pdo->query("SHOW COLUMNS FROM properties");
while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}
echo "\n--- BLOG POSTS ---\n";
$res = $pdo->query("SHOW COLUMNS FROM blog_posts");
while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}
?>
