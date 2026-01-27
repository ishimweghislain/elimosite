<?php
require_once '../includes/config.php';

require_login();

// Handle development operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_development'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            // First unlink properties linked to this development
            global $pdo;
            $pdo->prepare("UPDATE properties SET development_id = NULL WHERE development_id = ?")->execute([$id]);
            
            delete_record('developments', $id);
            header('Location: manage-developments.php?success=deleted');
            exit;
        }
    }
}

// Get developments
// Get developments (Regular users see their own, admins see all)
$search = clean_input($_GET['search'] ?? '');
$where = [];

if (!is_admin()) {
    $where[] = "created_by = " . (int)$_SESSION['user_id'];
}

if (!empty($search)) {
    $where[] = "(title LIKE '%$search%' OR location LIKE '%$search%')";
}

$where_clause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
$developments = get_records('developments', $where_clause . " ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developments Management - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Developments Management</h1>
                    </div>
                    <div class="d-flex">
                        <form method="GET" class="me-3 d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search developments..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="manage-developments.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                            <?php endif; ?>
                        </form>
                        <a href="development-edit.php" class="btn btn-primary shadow-sm">
                            <i class="fas fa-plus me-2"></i>Add Development
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'added': echo 'Development added successfully!'; break;
                            case 'updated': echo 'Development updated successfully!'; break;
                            case 'deleted': echo 'Development deleted successfully!'; break;
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
                                        <th>Banner</th>
                                        <th>Title</th>
                                        <th>Location</th>
                                        <th>Listings</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($developments)): ?>
                                        <?php foreach ($developments as $dev): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($dev['image_main'])): ?>
                                                        <img src="../images/<?php echo htmlspecialchars($dev['image_main']); ?>" class="rounded" style="width: 60px; height: 45px; object-fit: cover;" alt="<?php echo htmlspecialchars($dev['title']); ?>">
                                                    <?php else: ?>
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 45px;">
                                                            <i class="fas fa-city text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($dev['title']); ?></div>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($dev['location']); ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    global $pdo;
                                                    $count = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE development_id = ?");
                                                    $count->execute([$dev['id']]);
                                                    $listing_count = $count->fetchColumn();
                                                    ?>
                                                    <span class="badge bg-primary"><?php echo $listing_count; ?> Properties</span>
                                                </td>
                                                <td><small><?php echo date('M d, Y', strtotime($dev['created_at'])); ?></small></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="development-edit.php?id=<?php echo $dev['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this development? (Linked properties will remain but will be unlinked)');">
                                                            <input type="hidden" name="id" value="<?php echo $dev['id']; ?>">
                                                            <input type="hidden" name="delete_development" value="1">
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
                                                    <i class="fas fa-city fa-4x mb-3"></i>
                                                    <h5>No developments added yet</h5>
                                                    <p>Create a project to group properties.</p>
                                                </div>
                                                <a href="development-edit.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Development
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
</body>
</html>
