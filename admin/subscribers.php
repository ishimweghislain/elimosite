<?php
require_once '../includes/config.php';

require_admin();

// Handle subscriber operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_subscriber'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('newsletter_subscribers', $id);
            header('Location: subscribers.php?success=deleted');
            exit;
        }
    }
}

// Get subscribers
$subscribers = get_records('newsletter_subscribers', 'ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Subscribers</h1>
                    </div>
                    <div class="badge bg-primary rounded-pill px-3 py-2">
                        <?php echo count($subscribers); ?> Total Subscribers
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'deleted': echo 'Subscriber deleted successfully!'; break;
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
                                        <th>Date Subscribed</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($subscribers)): ?>
                                        <?php foreach ($subscribers as $subscriber): ?>
                                            <tr>
                                                <td>
                                                    <div class="text-dark"><?php echo format_date($subscriber['created_at'], 'M d, Y'); ?></div>
                                                    <div class="small text-muted"><?php echo format_date($subscriber['created_at'], 'H:i'); ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary"><?php echo htmlspecialchars($subscriber['email']); ?></div>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this subscriber?');">
                                                        <input type="hidden" name="id" value="<?php echo $subscriber['id']; ?>">
                                                        <input type="hidden" name="delete_subscriber" value="1">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-users-slash fa-4x mb-3"></i>
                                                    <h5>No subscribers yet</h5>
                                                    <p>When users subscribe to your newsletter, they will appear here.</p>
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

    <?php include 'includes/footer.php'; ?>
</html>
