<?php
require_once '../includes/config.php';

require_login();

// Handle property operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_property'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('properties', $id);
            header('Location: properties-new.php?success=deleted');
            exit;
        }
    }
    
    if (isset($_POST['toggle_featured'])) {
        $id = $_POST['id'] ?? 0;
        $property = get_record('properties', $id);
        if ($property) {
            $new_featured = $property['is_featured'] ? 0 : 1;
            update_record('properties', ['is_featured' => $new_featured], $id);
            header('Location: properties-new.php?success=updated');
            exit;
        }
    }
}

// Get properties (exclude drafts and separate developments)
// Get properties (Regular users see their own, admins see all published)
$where_clause = "WHERE status != 'draft' AND category != 'Developments'";
if (!is_admin()) {
    $where_clause = "WHERE created_by = " . (int)$_SESSION['user_id'] . " AND category != 'Developments'";
}
$properties = get_records('properties', $where_clause . " ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties Management - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Properties Management</h1>
                    </div>
                    <a href="property-edit-new.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add Property
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'added': echo 'Property added successfully!'; break;
                            case 'updated': echo 'Property updated successfully!'; break;
                            case 'deleted': echo 'Property deleted successfully!'; break;
                            default: echo htmlspecialchars($_GET['success']); break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger fade-in">
                        <?php echo htmlspecialchars($_GET['error']); ?>
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
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($properties)): ?>
                                        <?php foreach ($properties as $property): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($property['image_main'])): ?>
                                                        <img src="../images/<?php echo htmlspecialchars($property['image_main']); ?>" class="rounded" style="width: 60px; height: 45px; object-fit: cover;" alt="<?php echo htmlspecialchars($property['title']); ?>">
                                                    <?php else: ?>
                                                        <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 45px;">
                                                            <i class="fas fa-building text-secondary"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($property['title']); ?></div>
                                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($property['location']); ?></small>
                                                </td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($property['category']); ?></span></td>
                                                <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                                                <td class="fw-bold text-primary">
                                                    <?php if ($property['status'] === 'for-rent'): ?>
                                                        <?php echo number_format($property['price']); ?> RWF/mo
                                                    <?php else: ?>
                                                        <?php echo number_format($property['price']); ?> RWF
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_colors = [
                                                        'for-rent' => 'info',
                                                        'for-sale' => 'success', 
                                                        'under-construction' => 'warning',
                                                        'sold' => 'danger',
                                                        'rented' => 'secondary'
                                                    ];
                                                    $color = $status_colors[$property['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge bg-<?php echo $color; ?>">
                                                        <?php echo ucwords(str_replace('-', ' ', $property['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                                                        <input type="hidden" name="toggle_featured" value="1">
                                                        <button type="submit" class="btn btn-sm btn-link text-warning p-0" title="Toggle Featured">
                                                            <i class="fas fa-star <?php echo $property['is_featured'] ? '' : 'text-muted'; ?>" style="font-size: 1.2rem;"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="property-edit-new.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                                            <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                                                            <input type="hidden" name="delete_property" value="1">
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
                                            <td colspan="8" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-building fa-4x mb-3"></i>
                                                    <h5>No properties added yet</h5>
                                                    <p>Add your first property to get started.</p>
                                                </div>
                                                <a href="property-edit-new.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Property
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
