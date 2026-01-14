<?php
require_once '../includes/config.php';

require_admin();

$id = $_GET['id'] ?? 0;
$message = get_record('contact_messages', $id);

if ($message) {
    // Mark as read
    update_record('contact_messages', ['status' => 'read'], $id);
    
    echo '<div class="row">';
    echo '<div class="col-md-6"><strong>From:</strong> ' . htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) . '</div>';
    echo '<div class="col-md-6"><strong>Email:</strong> ' . htmlspecialchars($message['email']) . '</div>';
    echo '<div class="col-md-6"><strong>Phone:</strong> ' . htmlspecialchars($message['phone']) . '</div>';
    echo '<div class="col-md-6"><strong>Date:</strong> ' . format_date($message['created_at'], 'M d, Y H:i') . '</div>';
    echo '</div>';
    echo '<div class="mt-3"><strong>Status:</strong> ' . htmlspecialchars($message['status']) . '</div>';
    echo '<div class="mt-3"><strong>Message:</strong></div>';
    echo '<div class="border p-3 bg-light">' . nl2br(htmlspecialchars($message['message'])) . '</div>';
} else {
    echo '<p class="text-danger">Message not found.</p>';
}
?>
