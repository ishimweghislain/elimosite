<?php
require_once '../includes/config.php';

require_login();

// Handle blog operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_blog'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('blog_posts', $id);
            header('Location: blog.php?success=deleted');
            exit;
        }
    }
}

// Get blog posts (exclude drafts)
// Get blog posts (Regular users see their own, admins see all published/others)
$where_clause = "WHERE status != 'draft'";
if (!is_admin()) {
    $where_clause = "WHERE created_by = " . (int)$_SESSION['user_id'];
}
$blog_posts = get_records('blog_posts', $where_clause . " ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Blog Management</h1>
                    </div>
                    <a href="blog-edit.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add Blog Post
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'added': echo 'Blog post added successfully!'; break;
                            case 'updated': echo 'Blog post updated successfully!'; break;
                            case 'deleted': echo 'Blog post deleted successfully!'; break;
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
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($blog_posts)): ?>
                                        <?php foreach ($blog_posts as $post): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($post['image'])): ?>
                                                        <img src="../images/<?php echo htmlspecialchars($post['image']); ?>" class="rounded" style="width: 60px; height: 40px; object-fit: cover;" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                                    <?php else: ?>
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                                            <i class="fas fa-blog text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($post['title']); ?></div>
                                                </td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($post['category']); ?></span></td>
                                                <td>
                                                    <?php if ($post['status'] === 'published'): ?>
                                                        <span class="badge bg-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo format_date($post['created_at'], 'M d, Y'); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="blog-edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                                            <input type="hidden" name="delete_blog" value="1">
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
                                                    <i class="fas fa-blog fa-4x mb-3"></i>
                                                    <h5>No blog posts added yet</h5>
                                                    <p>Add your first blog post to get started.</p>
                                                </div>
                                                <a href="blog-edit.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Blog Post
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
