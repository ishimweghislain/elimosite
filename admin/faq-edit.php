<?php
require_once '../includes/config.php';

require_admin();

$faq = null;
$edit_mode = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $faq = get_record('faqs', $id);
    $edit_mode = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'question' => clean_input($_POST['question'] ?? ''),
        'answer' => clean_input($_POST['answer'] ?? ''),
        'category' => clean_input($_POST['category'] ?? 'general'),
        'order_index' => intval($_POST['order'] ?? 0)
    ];

    if ($edit_mode) {
        // Update existing FAQ
        $result = update_record('faqs', $data, $id);
        $message = 'updated';
    } else {
        // Add new FAQ
        $result = insert_record('faqs', $data);
        $message = 'added';
    }

    if ($result) {
        header('Location: faqs.php?success=' . $message);
        exit;
    } else {
        $error = 'Failed to save FAQ. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> FAQ - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> FAQ</h1>
                    </div>
                    <a href="faqs.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to FAQs
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger fade-in"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label">Question *</label>
                                <input type="text" name="question" class="form-control" value="<?php echo htmlspecialchars($faq['question'] ?? ''); ?>" required placeholder="e.g. How do I schedule a viewing?">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Answer *</label>
                                <textarea name="answer" class="form-control" rows="5" required placeholder="Provide a detailed answer..."><?php echo htmlspecialchars($faq['answer'] ?? ''); ?></textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="general" <?php echo ($faq['category'] ?? '') === 'general' ? 'selected' : ''; ?>>General</option>
                                        <option value="selling" <?php echo ($faq['category'] ?? '') === 'selling' ? 'selected' : ''; ?>>Selling</option>
                                        <option value="renting" <?php echo ($faq['category'] ?? '') === 'renting' ? 'selected' : ''; ?>>Renting</option>
                                        <option value="developments" <?php echo ($faq['category'] ?? '') === 'developments' ? 'selected' : ''; ?>>Developments</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="order" class="form-control" value="<?php echo $faq['order'] ?? 0; ?>" min="0">
                                    <div class="form-text text-muted">Lower numbers appear first</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <a href="faqs.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i><?php echo $edit_mode ? 'Update FAQ' : 'Save FAQ'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>
