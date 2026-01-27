<?php
require_once '../includes/config.php';
require_login();

// Handle property actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_property'])) {
        $property_id = (int)$_POST['property_id'];
        if (delete_record('properties', ['id' => $property_id])) {
            $message = '<div class="alert alert-success">Property deleted successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to delete property.</div>';
        }
    }
}

// Get properties with pagination
$page = (int)($_GET['page'] ?? 1);
$per_page = 10;
// Get properties (Regular users see their own, admins see all)
$search = clean_input($_GET['search'] ?? '');
$where = [];
if (!is_admin()) {
    $where['created_by'] = $_SESSION['user_id'];
}
if (!empty($search)) {
    $where['search'] = $search;
}
$properties_data = get_properties($where, $page, $per_page);
$properties = $properties_data['properties'];
$total_pages = $properties_data['total_pages'];
$current_page = $properties_data['page'];

// Handle edit mode
$edit_property = null;
if (isset($_GET['edit'])) {
    $edit_property = get_property((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - <?php echo get_setting('site_name'); ?></title>
    <?php include 'includes/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="h2 mb-0">Manage Properties</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <form method="GET" class="me-3 d-flex">
                            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search properties..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="properties.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                            <?php endif; ?>
                        </form>
                        <div class="btn-group me-2">
                            <a href="properties.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Property
                            </a>
                        </div>
                    </div>
                </div>

                <?php echo $message; ?>

                <?php if ($edit_property): ?>
                    <!-- Edit Property Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Property</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="property-save.php">
                                <input type="hidden" name="property_id" value="<?php echo $edit_property['id']; ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($edit_property['title']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="Residential" <?php echo $edit_property['category'] === 'Residential' ? 'selected' : ''; ?>>Residential</option>
                                            <option value="Commercial" <?php echo $edit_property['category'] === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                                            <option value="Developments" <?php echo $edit_property['category'] === 'Developments' ? 'selected' : ''; ?>>Developments</option>
                                            <option value="Land" <?php echo $edit_property['category'] === 'Land' ? 'selected' : ''; ?>>Land</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="property_type" class="form-label">Property Type</label>
                                        <select class="form-control" id="property_type" name="property_type" required>
                                            <option value="Apartment" <?php echo $edit_property['property_type'] === 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                                            <option value="House" <?php echo $edit_property['property_type'] === 'House' ? 'selected' : ''; ?>>House</option>
                                            <option value="Townhouse" <?php echo $edit_property['property_type'] === 'Townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                                            <option value="Semi Detached" <?php echo $edit_property['property_type'] === 'Semi Detached' ? 'selected' : ''; ?>>Semi Detached</option>
                                            <option value="Office" <?php echo $edit_property['property_type'] === 'Office' ? 'selected' : ''; ?>>Office</option>
                                            <option value="Retail" <?php echo $edit_property['property_type'] === 'Retail' ? 'selected' : ''; ?>>Retail</option>
                                            <option value="Industrial" <?php echo $edit_property['property_type'] === 'Industrial' ? 'selected' : ''; ?>>Industrial</option>
                                            <option value="Land" <?php echo $edit_property['property_type'] === 'Land' ? 'selected' : ''; ?>>Land</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="for-rent" <?php echo $edit_property['status'] === 'for-rent' ? 'selected' : ''; ?>>For Rent</option>
                                            <option value="for-sale" <?php echo $edit_property['status'] === 'for-sale' ? 'selected' : ''; ?>>For Sale</option>
                                            <option value="under-construction" <?php echo $edit_property['status'] === 'under-construction' ? 'selected' : ''; ?>>Under Construction</option>
                                            <option value="sold" <?php echo $edit_property['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                                            <option value="rented" <?php echo $edit_property['status'] === 'rented' ? 'selected' : ''; ?>>Rented</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">Price (RWF)</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="<?php echo $edit_property['price']; ?>" step="0.01">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?php echo htmlspecialchars($edit_property['location']); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="province" class="form-label">Province</label>
                                        <input type="text" class="form-control" id="province" name="province" 
                                               value="<?php echo htmlspecialchars($edit_property['province']); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="bedrooms" class="form-label">Bedrooms</label>
                                        <input type="number" class="form-control" id="bedrooms" name="bedrooms" 
                                               value="<?php echo $edit_property['bedrooms']; ?>" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="bathrooms" class="form-label">Bathrooms</label>
                                        <input type="number" class="form-control" id="bathrooms" name="bathrooms" 
                                               value="<?php echo $edit_property['bathrooms']; ?>" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="garage" class="form-label">Parking Space</label>
                                        <input type="number" class="form-control" id="garage" name="garage" 
                                               value="<?php echo $edit_property['garage']; ?>" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="size_sqm" class="form-label">Build Size (m²)</label>
                                        <input type="number" class="form-control" id="size_sqm" name="size_sqm" 
                                               value="<?php echo $edit_property['size_sqm']; ?>" step="0.01">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($edit_property['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                               <?php echo $edit_property['is_featured'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured Property
                                        </label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="properties.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Properties List -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Type</th>
                                            <th>Location</th>
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
                                                        <a href="properties.php?edit=<?php echo $property['id']; ?>">
                                                            <?php echo htmlspecialchars($property['title']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($property['category']); ?></td>
                                                    <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                                                    <td><?php echo htmlspecialchars($property['location']); ?></td>
                                                    <td><?php echo format_price($property['price']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            echo match($property['status']) {
                                                                'for-rent' => 'info',
                                                                'for-sale' => 'success',
                                                                'under-construction' => 'warning',
                                                                'sold' => 'danger',
                                                                'rented' => 'secondary',
                                                                default => 'secondary'
                                                            }; ?>">
                                                            <?php echo htmlspecialchars($property['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($property['is_featured']): ?>
                                                            <span class="badge badge-warning">Yes</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-light">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="properties.php?edit=<?php echo $property['id']; ?>" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                                                                <input type="hidden" name="delete_property" value="1">
                                                                <button type="submit" class="btn btn-outline-danger" 
                                                                        onclick="return confirm('Are you sure you want to delete this property?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No properties found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>
