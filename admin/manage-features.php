<?php
require_once '../includes/config.php';
require_admin();

// Handle add/edit/delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $name = clean_input($_POST['name']);
        $type = clean_input($_POST['type']);
        
        if (!empty($name) && in_array($type, ['feature', 'amenity', 'ideal_for'])) {
            $data = [
                'name' => $name,
                'type' => $type,
                'is_active' => 1
            ];
            
            if (insert_record('property_features_master', $data)) {
                $success = ucfirst($type) . ' added successfully!';
            } else {
                $error = 'Failed to add ' . $type . '. It may already exist.';
            }
        }
    } elseif (isset($_POST['toggle_status'])) {
        $id = (int)$_POST['id'];
        $current_status = (int)$_POST['current_status'];
        $new_status = $current_status ? 0 : 1;
        
        if (update_record('property_features_master', ['is_active' => $new_status], $id)) {
            $success = 'Status updated successfully!';
        }
    } elseif (isset($_POST['delete_item'])) {
        $id = (int)$_POST['id'];
        
        if (delete_record('property_features_master', $id)) {
            $success = 'Item deleted successfully!';
        } else {
            $error = 'Failed to delete item.';
        }
    }
}

// Fetch all features and amenities
global $pdo;
$stmt = $pdo->query("SELECT * FROM property_features_master WHERE type = 'feature' ORDER BY name ASC");
$features = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM property_features_master WHERE type = 'amenity' ORDER BY name ASC");
$amenities = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM property_features_master WHERE type = 'ideal_for' ORDER BY name ASC");
$ideal_fors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Features & Amenities - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <h1 class="h2 fw-bold text-dark">Manage Features & Amenities</h1>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Add New Item Form -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Feature or Amenity</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Swimming Pool" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="feature">Property Feature</option>
                                    <option value="amenity">Amenity</option>
                                    <option value="ideal_for">Ideal For Option</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" name="add_item" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Add Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Features Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Property Features</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($features)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($features as $feature): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($feature['name']); ?>
                                                </td>
                                                <td>
                                                    <?php if ($feature['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($feature['created_at'])); ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $feature['id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?php echo $feature['is_active']; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-<?php echo $feature['is_active'] ? 'warning' : 'success'; ?>" title="Toggle Status">
                                                            <i class="fas fa-<?php echo $feature['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this feature?');">
                                                        <input type="hidden" name="id" value="<?php echo $feature['id']; ?>">
                                                        <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No features added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Amenities Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Amenities</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($amenities)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($amenities as $amenity): ?>
                                            <tr>
                                                <td>
                                                    <i class="fas fa-star text-warning me-2"></i>
                                                    <?php echo htmlspecialchars($amenity['name']); ?>
                                                </td>
                                                <td>
                                                    <?php if ($amenity['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($amenity['created_at'])); ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $amenity['id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?php echo $amenity['is_active']; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-<?php echo $amenity['is_active'] ? 'warning' : 'success'; ?>" title="Toggle Status">
                                                            <i class="fas fa-<?php echo $amenity['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this amenity?');">
                                                        <input type="hidden" name="id" value="<?php echo $amenity['id']; ?>">
                                                        <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No amenities added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Ideal For Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Ideal For Options</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($ideal_fors)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ideal_fors as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td><?php echo $item['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?php echo $item['is_active']; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sync"></i></button>
                                                    </form>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete?');">
                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" name="delete_item" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">No 'Ideal For' options added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
