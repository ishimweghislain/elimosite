<?php
require_once '../includes/config.php';

require_admin();

// Handle FAQ operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_faq'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('faqs', $id);
            header('Location: faqs.php?success=deleted');
            exit;
        }
    }
}

// Get FAQs
$faqs = get_records('faqs', 'ORDER BY order_index ASC, created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Management - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">FAQ Management</h1>
                    </div>
                    <a href="faq-edit.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add FAQ
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'added': echo 'FAQ added successfully!'; break;
                            case 'updated': echo 'FAQ updated successfully!'; break;
                            case 'deleted': echo 'FAQ deleted successfully!'; break;
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
                                        <th>Question</th>
                                        <th>Answer</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($faqs)): ?>
                                        <?php foreach ($faqs as $faq): ?>
                                            <tr>
                                                <td style="width: 30%;">
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($faq['question']); ?></div>
                                                </td>
                                                <td style="width: 50%;">
                                                    <div class="text-muted text-truncate" style="max-width: 400px;"><?php echo htmlspecialchars($faq['answer']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($faq['order_index']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="faq-edit.php?id=<?php echo $faq['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                                                            <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                                                            <input type="hidden" name="delete_faq" value="1">
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
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-question-circle fa-4x mb-3"></i>
                                                    <h5>No FAQs added yet</h5>
                                                    <p>Add frequently asked questions to help your users.</p>
                                                </div>
                                                <a href="faq-edit.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add FAQ
                                                </a>
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
