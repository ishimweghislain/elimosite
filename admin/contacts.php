<?php
require_once '../includes/config.php';

require_admin();

// Handle contact operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_contact'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('contact_messages', $id);
            header('Location: contacts.php?success=deleted');
            exit;
        }
    }
    
    if (isset($_POST['mark_read'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            update_record('contact_messages', ['status' => 'read'], $id);
            header('Location: contacts.php?success=marked_read');
            exit;
        }
    }
}

// Get contact messages
$contacts = get_records('contact_messages', 'ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .unread {
            background-color: rgba(78, 115, 223, 0.05);
            font-weight: 500;
        }
        .message-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="h2 fw-bold text-dark mb-0">Contact Messages</h1>
                    </div>
                    <div class="badge bg-primary rounded-pill px-3 py-2">
                        <?php echo count($contacts); ?> Total Messages
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'marked_read': echo 'Message marked as read!'; break;
                            case 'deleted': echo 'Message deleted successfully!'; break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($contacts)): ?>
                                        <?php foreach ($contacts as $contact): ?>
                                            <tr class="<?php echo $contact['status'] === 'new' ? 'unread' : ''; ?>">
                                                <td>
                                                    <div class="small text-muted"><?php echo format_date($contact['created_at'], 'M d, Y'); ?></div>
                                                    <div class="small text-muted"><?php echo format_date($contact['created_at'], 'H:i'); ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></div>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($contact['email']); ?></div>
                                                    <?php if (!empty($contact['phone'])): ?>
                                                        <div class="small text-muted"><?php echo htmlspecialchars($contact['phone']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($contact['status'] === 'read' || $contact['status'] === 'replied'): ?>
                                                        <span class="badge bg-secondary">Read</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">New</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="message-preview text-muted" title="<?php echo htmlspecialchars($contact['message']); ?>">
                                                        <?php echo htmlspecialchars($contact['message']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <?php if ($contact['status'] === 'new'): ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                                <input type="hidden" name="mark_read" value="1">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as read">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                        <button class="btn btn-sm btn-outline-primary ms-1" onclick="viewMessage(<?php echo $contact['id']; ?>)" title="View full message">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                                            <input type="hidden" name="delete_contact" value="1">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-1" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-envelope fa-4x mb-3"></i>
                                                    <h5>No contact messages yet</h5>
                                                    <p>When users submit the contact form, messages will appear here.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageContent">
                    <!-- Message content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewMessage(id) {
            fetch('get-message.php?id=' + id)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('messageContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('messageModal')).show();
                });
        }
    </script>
    <?php include 'includes/footer.php'; ?>
