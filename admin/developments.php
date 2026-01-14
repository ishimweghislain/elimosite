<?php
require_once '../includes/config.php';

require_admin();

// Handle delete property
if (isset($_POST['delete_property'])) {
    $id = $_POST['id'] ?? 0;
    if ($id > 0) {
        // Get image to delete
        $property = get_record('properties', $id);
        if ($property && !empty($property['image_main'])) {
            $image_path = '../images/' . $property['image_main'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        delete_record('properties', $id);
        header('Location: developments.php?success=deleted');
        exit;
    }
}

// Get development properties (exclude drafts)
$properties = get_records('properties', "WHERE category = 'Developments' AND status != 'draft' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developments - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .property-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3em 0.8em;
            border-radius: 20px;
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
                        <h1 class="h2 fw-bold text-dark mb-0">Developments</h1>
                    </div>
                    <a href="property-edit-new.php?category=Developments" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Development
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php 
                        if ($_GET['success'] == 'deleted') echo 'Development deleted successfully!';
                        else echo 'Operation successful!';
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
                                        <th>Type</th>
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
                                                        <img src="../images/<?php echo htmlspecialchars($property['image_main']); ?>" class="property-img" alt="Property">
                                                    <?php else: ?>
                                                        <div class="bg-light d-flex align-items-center justify-content-center property-img text-muted">
                                                            <i class="fas fa-home"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($property['title']); ?></div>
                                                    <div class="small text-muted"><?php echo format_price($property['price']); ?></div>
                                                </td>
                                                <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClasses = [
                                                        'for-rent' => 'bg-info text-dark',
                                                        'for-sale' => 'bg-success text-white',
                                                        'under-construction' => 'bg-warning text-dark',
                                                        'sold' => 'bg-danger text-white',
                                                        'rented' => 'bg-secondary text-white',
                                                        'draft' => 'bg-dark text-white'
                                                    ];
                                                    $statusClass = $statusClasses[$property['status']] ?? 'bg-light text-dark';
                                                    $statusLabel = ucwords(str_replace('-', ' ', $property['status']));
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?> status-badge">
                                                        <?php echo $statusLabel; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($property['is_featured']): ?>
                                                        <i class="fas fa-star text-warning" title="Featured"></i>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="property-edit-new.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this development?');">
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
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-hard-hat fa-4x mb-3"></i>
                                                    <h5>No developments found</h5>
                                                    <p>Add a new development property to get started.</p>
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
