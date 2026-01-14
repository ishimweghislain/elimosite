<?php
require_once '../includes/config.php';

require_admin();

// Handle inquiry operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_inquiry'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('property_inquiries', $id);
            header('Location: inquiries.php?success=deleted');
            exit;
        }
    }
    
    if (isset($_POST['update_status'])) {
        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        if ($id > 0 && in_array($status, ['new', 'contacted', 'closed'])) {
            update_record('property_inquiries', ['status' => $status], $id);
            header('Location: inquiries.php?success=updated');
            exit;
        }
    }
}

// Get inquiries with property details
$sql = "SELECT i.*, p.title as property_title 
        FROM property_inquiries i 
        LEFT JOIN properties p ON i.property_id = p.id 
        ORDER BY i.created_at DESC";
$stmt = $pdo->query($sql);
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inquiries - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .unread {
            background-color: rgba(78, 115, 223, 0.05);
            font-weight: 500;
        }
        .message-preview {
            max-width: 250px;
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
                        <h1 class="h2 fw-bold text-dark mb-0">Property Inquiries</h1>
                    </div>
                    <div class="badge bg-primary rounded-pill px-3 py-2">
                        <?php echo count($inquiries); ?> Total Inquiries
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'updated': echo 'Inquiry status updated successfully!'; break;
                            case 'deleted': echo 'Inquiry deleted successfully!'; break;
                            default: echo htmlspecialchars($_GET['success']); break;
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
                                        <th>Property</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inquiries)): ?>
                                        <?php foreach ($inquiries as $inquiry): ?>
                                            <tr class="<?php echo $inquiry['status'] === 'new' ? 'unread' : ''; ?>">
                                                <td>
                                                    <div class="small text-muted"><?php echo format_date($inquiry['created_at'], 'M d, Y'); ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($inquiry['property_title'] ?? 'Unknown Property'); ?></div>
                                                    <div class="small text-muted">ID: #<?php echo $inquiry['property_id']; ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($inquiry['name']); ?></div>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($inquiry['email']); ?></div>
                                                    <?php if (!empty($inquiry['phone'])): ?>
                                                        <div class="small text-muted"><?php echo htmlspecialchars($inquiry['phone']); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                        <input type="hidden" name="update_status" value="1">
                                                        <select name="status" class="form-select form-select-sm <?php echo $inquiry['status'] == 'new' ? 'bg-warning text-dark' : ($inquiry['status'] == 'contacted' ? 'bg-info text-dark' : 'bg-success text-white'); ?>" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                                            <option value="new" <?php echo $inquiry['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                                            <option value="contacted" <?php echo $inquiry['status'] == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                            <option value="closed" <?php echo $inquiry['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div class="message-preview text-muted" title="<?php echo htmlspecialchars($inquiry['message']); ?>">
                                                        <?php echo htmlspecialchars($inquiry['message']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="viewInquiry(<?php echo htmlspecialchars(json_encode($inquiry)); ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                                            <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
                                                            <input type="hidden" name="delete_inquiry" value="1">
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
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-comment-dollar fa-4x mb-3"></i>
                                                    <h5>No property inquiries yet</h5>
                                                    <p>Inquiries about your properties will appear here.</p>
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

    <!-- Inquiry Modal -->
    <div class="modal fade" id="inquiryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inquiry Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-muted mb-2">Property</h6>
                    <p id="modalProperty" class="fw-bold mb-4"></p>
                    
                    <h6 class="text-muted mb-2">Client Details</h6>
                    <p class="mb-1"><strong>Name:</strong> <span id="modalName"></span></p>
                    <p class="mb-1"><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p class="mb-4"><strong>Phone:</strong> <span id="modalPhone"></span></p>
                    
                    <h6 class="text-muted mb-2">Message</h6>
                    <p id="modalMessage" class="bg-light p-3 rounded"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewInquiry(data) {
            document.getElementById('modalProperty').textContent = data.property_title || 'Unknown Property';
            document.getElementById('modalName').textContent = data.name;
            document.getElementById('modalEmail').textContent = data.email;
            document.getElementById('modalPhone').textContent = data.phone || 'N/A';
            document.getElementById('modalMessage').textContent = data.message || 'No message provided';
            
            new bootstrap.Modal(document.getElementById('inquiryModal')).show();
        }
    </script>
    <?php include 'includes/footer.php'; ?>
</html>
