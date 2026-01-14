<?php
require_once '../includes/config.php';

require_admin();

$property = null;
$edit_mode = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $property = get_record('properties', $id);
    $edit_mode = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => clean_input($_POST['title'] ?? ''),
        'description' => clean_input($_POST['description'] ?? ''),
        'category' => clean_input($_POST['category'] ?? 'Residential'),
        'property_type' => clean_input($_POST['property_type'] ?? 'Apartment'),
        'status' => isset($_POST['save_draft']) ? 'draft' : clean_input($_POST['status'] ?? 'for-rent'),
        'price' => floatval($_POST['price'] ?? 0),
        'location' => clean_input($_POST['location'] ?? ''),
        'province' => clean_input($_POST['province'] ?? ''),
        'district' => clean_input($_POST['district'] ?? ''),
        'bedrooms' => intval($_POST['bedrooms'] ?? 0),
        'bathrooms' => intval($_POST['bathrooms'] ?? 0),
        'garage' => intval($_POST['garage'] ?? 0),
        'size_sqm' => floatval($_POST['size_sqm'] ?? 0),
        'year_built' => intval($_POST['year_built'] ?? date('Y')),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        // Added fields
        'prop_id' => clean_input($_POST['prop_id'] ?? ''),
        'stories' => intval($_POST['stories'] ?? 0),
        'furnished' => clean_input($_POST['furnished'] ?? ''),
        'multi_family' => clean_input($_POST['multi_family'] ?? ''),
        'plot_size' => floatval($_POST['plot_size'] ?? 0),
        'zoning' => clean_input($_POST['zoning'] ?? ''),
        'views' => clean_input($_POST['views'] ?? ''),
        'ideal_for' => clean_input($_POST['ideal_for'] ?? ''),
        'proximity' => clean_input($_POST['proximity'] ?? ''),
        'features' => isset($_POST['features']) ? json_encode($_POST['features']) : json_encode([]),
        'amenities' => isset($_POST['amenities']) ? json_encode($_POST['amenities']) : json_encode([])
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['image'], '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024); // 5MB
        if ($upload_result['success']) {
            $data['image_main'] = $upload_result['filename'];
        }
    }

    // Handle sub-images upload
    $current_sub_images = [];
    if ($edit_mode && !empty($property['images'])) {
        $current_sub_images = json_decode($property['images'], true);
        if (!is_array($current_sub_images)) $current_sub_images = [];
    }

    // Check if any sub-images were marked for deletion
    if (isset($_POST['remove_sub_images']) && is_array($_POST['remove_sub_images'])) {
        foreach ($_POST['remove_sub_images'] as $img_to_remove) {
            if (($key = array_search($img_to_remove, $current_sub_images)) !== false) {
                unset($current_sub_images[$key]);
                // Optional: delete physical file
                // @unlink('../images/' . $img_to_remove);
            }
        }
        $current_sub_images = array_values($current_sub_images);
    }

    if (isset($_FILES['sub_images'])) {
        foreach ($_FILES['sub_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['sub_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['sub_images']['name'][$key],
                    'type' => $_FILES['sub_images']['type'][$key],
                    'tmp_name' => $_FILES['sub_images']['tmp_name'][$key],
                    'error' => $_FILES['sub_images']['error'][$key],
                    'size' => $_FILES['sub_images']['size'][$key]
                ];
                $upload_result = upload_file($file, '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024);
                if ($upload_result['success']) {
                    $current_sub_images[] = $upload_result['filename'];
                }
            }
        }
    }
    $data['images'] = json_encode($current_sub_images);

    if ($edit_mode) {
        // Update existing property
        $result = update_record('properties', $data, $id);
        $message = 'updated';
    } else {
        // Add new property
        $result = insert_record('properties', $data);
        $message = 'added';
    }

    if ($result) {
        $redirect_url = 'properties-new.php';
        if ($data['status'] === 'draft') {
            $redirect_url = 'drafts.php';
        } elseif ($data['category'] === 'Developments') {
            $redirect_url = 'developments.php';
        }
        
        header('Location: ' . $redirect_url . '?success=' . $message);
        exit;
    } else {
        $error = 'Failed to save property. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Property - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .preview-img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            border: 2px solid #e3e6f0;
        }
        .sub-image-preview {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .sub-image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .remove-img-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            line-height: 20px;
            text-align: center;
            font-size: 14px;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .remove-img-btn:hover {
            background: #ff6b81;
        }
        .new-images-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 8px;
        }
        .new-img-item {
            position: relative;
            width: 80px;
            height: 80px;
        }
        .new-img-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
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
                        <h1 class="h2 fw-bold text-dark mb-0"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Property</h1>
                    </div>
                    <?php 
                    $back_url = 'properties-new.php';
                    if ($property && $property['category'] === 'Developments') $back_url = 'developments.php';
                    ?>
                    <a href="<?php echo $back_url; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to <?php echo ($property && $property['category'] === 'Developments') ? 'Developments' : 'Properties'; ?>
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger fade-in"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <h5 class="mb-4 text-primary fw-bold">Basic Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">Property Title *</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($property['title'] ?? ''); ?>" required placeholder="e.g. Luxury Apartment in Kigali">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Price (RWF) *</label>
                                    <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($property['price'] ?? ''); ?>" required min="0" placeholder="0.00">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <?php 
                                $current_category = $property['category'] ?? ($_GET['category'] ?? 'Residential');
                                ?>
                                <div class="col-md-4">
                                    <label class="form-label">Category *</label>
                                    <select name="category" class="form-select" required>
                                        <option value="Residential" <?php echo $current_category === 'Residential' ? 'selected' : ''; ?>>Residential</option>
                                        <option value="Commercial" <?php echo $current_category === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                                        <option value="Developments" <?php echo $current_category === 'Developments' ? 'selected' : ''; ?>>Developments</option>
                                        <option value="Land" <?php echo $current_category === 'Land' ? 'selected' : ''; ?>>Land</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Property Type *</label>
                                    <select name="property_type" class="form-select" required>
                                        <option value="Apartment" <?php echo ($property['property_type'] ?? '') === 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                                        <option value="House" <?php echo ($property['property_type'] ?? '') === 'House' ? 'selected' : ''; ?>>House</option>
                                        <option value="Townhouse" <?php echo ($property['property_type'] ?? '') === 'Townhouse' ? 'selected' : ''; ?>>Townhouse</option>
                                        <option value="Semi Detached" <?php echo ($property['property_type'] ?? '') === 'Semi Detached' ? 'selected' : ''; ?>>Semi Detached</option>
                                        <option value="Office" <?php echo ($property['property_type'] ?? '') === 'Office' ? 'selected' : ''; ?>>Office</option>
                                        <option value="Retail" <?php echo ($property['property_type'] ?? '') === 'Retail' ? 'selected' : ''; ?>>Retail</option>
                                        <option value="Industrial" <?php echo ($property['property_type'] ?? '') === 'Industrial' ? 'selected' : ''; ?>>Industrial</option>
                                        <option value="Land" <?php echo ($property['property_type'] ?? '') === 'Land' ? 'selected' : ''; ?>>Land</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status *</label>
                                    <select name="status" id="statusSelect" class="form-select" required>
                                        <option value="draft" <?php echo ($property['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="for-rent" <?php echo ($property['status'] ?? '') === 'for-rent' ? 'selected' : ''; ?>>For Rent</option>
                                        <option value="for-sale" <?php echo ($property['status'] ?? '') === 'for-sale' ? 'selected' : ''; ?>>For Sale</option>
                                        <option value="under-construction" <?php echo ($property['status'] ?? '') === 'under-construction' ? 'selected' : ''; ?>>Under Construction</option>
                                        <option value="sold" <?php echo ($property['status'] ?? '') === 'sold' ? 'selected' : ''; ?>>Sold</option>
                                        <option value="rented" <?php echo ($property['status'] ?? '') === 'rented' ? 'selected' : ''; ?>>Rented</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-4 text-primary fw-bold">Location & Details</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Location/Address *</label>
                                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($property['location'] ?? ''); ?>" required placeholder="e.g. Kiyovu, Kigali">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province *</label>
                                    <select name="province" id="provinceSelect" class="form-select" required>
                                        <option value="">Select Province</option>
                                        <?php 
                                        $provinces = ['Kigali City', 'Northern Province', 'Southern Province', 'Eastern Province', 'Western Province'];
                                        foreach ($provinces as $p) {
                                            $selected = ($property['province'] ?? '') === $p ? 'selected' : '';
                                            echo "<option value=\"$p\" $selected>$p</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">District *</label>
                                    <select name="district" id="districtSelect" class="form-select" required>
                                        <option value="">Select District</option>
                                        <?php if (!empty($property['district'])): ?>
                                            <option value="<?php echo htmlspecialchars($property['district']); ?>" selected><?php echo htmlspecialchars($property['district']); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Bedrooms</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                        <input type="number" name="bedrooms" class="form-control" value="<?php echo htmlspecialchars($property['bedrooms'] ?? 0); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bathrooms</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bath"></i></span>
                                        <input type="number" name="bathrooms" class="form-control" value="<?php echo htmlspecialchars($property['bathrooms'] ?? 0); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Garage</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-car"></i></span>
                                        <input type="number" name="garage" class="form-control" value="<?php echo htmlspecialchars($property['garage'] ?? 0); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Size (sqm)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-ruler-combined"></i></span>
                                        <input type="number" name="size_sqm" class="form-control" value="<?php echo htmlspecialchars($property['size_sqm'] ?? 0); ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Property ID</label>
                                    <input type="text" name="prop_id" class="form-control" value="<?php echo htmlspecialchars($property['prop_id'] ?? ''); ?>" placeholder="e.g. P65327">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Stories</label>
                                    <input type="number" name="stories" class="form-control" value="<?php echo htmlspecialchars($property['stories'] ?? 1); ?>" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Year Built</label>
                                    <input type="number" name="year_built" class="form-control" value="<?php echo htmlspecialchars($property['year_built'] ?? date('Y')); ?>" min="1900" max="<?php echo date('Y') + 10; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" <?php echo ($property['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="isFeatured">Featured Property</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-4 text-primary fw-bold">Additional Details</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Furnished</label>
                                    <select name="furnished" class="form-select">
                                        <option value="">Select Option</option>
                                        <option value="Fully Furnished" <?php echo ($property['furnished'] ?? '') === 'Fully Furnished' ? 'selected' : ''; ?>>Fully Furnished</option>
                                        <option value="Semi Furnished" <?php echo ($property['furnished'] ?? '') === 'Semi Furnished' ? 'selected' : ''; ?>>Semi Furnished</option>
                                        <option value="Unfurnished" <?php echo ($property['furnished'] ?? '') === 'Unfurnished' ? 'selected' : ''; ?>>Unfurnished</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Multi-family</label>
                                    <select name="multi_family" class="form-select">
                                        <option value="">Select Option</option>
                                        <option value="Yes" <?php echo ($property['multi_family'] ?? '') === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                        <option value="No" <?php echo ($property['multi_family'] ?? '') === 'No' ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Plot Size (sqm)</label>
                                    <input type="number" name="plot_size" class="form-control" value="<?php echo htmlspecialchars($property['plot_size'] ?? 0); ?>" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Zoning</label>
                                    <input type="text" name="zoning" class="form-control" value="<?php echo htmlspecialchars($property['zoning'] ?? ''); ?>" placeholder="e.g. Permit">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Views</label>
                                    <input type="text" name="views" class="form-control" value="<?php echo htmlspecialchars($property['views'] ?? ''); ?>" placeholder="e.g. City">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ideal for on Rentals</label>
                                    <input type="text" name="ideal_for" class="form-control" value="<?php echo htmlspecialchars($property['ideal_for'] ?? ''); ?>" placeholder="e.g. Single person, Couple">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">In close proximity to</label>
                                    <textarea name="proximity" class="form-control" rows="1"><?php echo htmlspecialchars($property['proximity'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <hr class="my-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <h5 class="mb-4 text-primary fw-bold">Property Features</h5>
                                    <?php 
                                    $features = json_decode($property['features'] ?? '[]', true);
                                    if (!is_array($features)) $features = [];
                                    $feature_list = [
                                        'Air Conditioner', 'Optic Fiber', 'Built in wardrobes', 
                                        'Proximity to schools', 'Tarmac road', 'Proximity to shops', 
                                        'Proximity to public transport', 'Water Tank', 'Garden', 'Open Plan Kitchen'
                                    ];
                                    ?>
                                    <div class="row">
                                        <?php foreach ($feature_list as $feature): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="features[]" value="<?php echo $feature; ?>" id="feat_<?php echo md5($feature); ?>" <?php echo in_array($feature, $features) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="feat_<?php echo md5($feature); ?>">
                                                        <?php echo $feature; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-4 text-primary fw-bold">Amenities</h5>
                                    <?php 
                                    $amenities = json_decode($property['amenities'] ?? '[]', true);
                                    if (!is_array($amenities)) $amenities = [];
                                    $amenity_list = ['Cleaning services', 'Laundry', 'Garbage collection', 'Security'];
                                    ?>
                                    <div class="row">
                                        <?php foreach ($amenity_list as $amenity): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="<?php echo $amenity; ?>" id="amen_<?php echo md5($amenity); ?>" <?php echo in_array($amenity, $amenities) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="amen_<?php echo md5($amenity); ?>">
                                                        <?php echo $amenity; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-4 text-primary fw-bold">Description & Media</h5>

                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="5" placeholder="Detailed description of the property..."><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Property Main Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <?php if ($edit_mode && !empty($property['image_main'])): ?>
                                        <div class="mt-2">
                                            <img src="../images/<?php echo htmlspecialchars($property['image_main']); ?>" class="preview-img" alt="Current image">
                                            <div class="small text-muted mt-1">Current Main Image</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Other Gallery Images (Sub-images)</label>
                                    <div class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Hold <strong>Ctrl</strong> (or <strong>Cmd</strong>) to select multiple images at once.</div>
                                    <input type="file" name="sub_images[]" id="subImagesInput" class="form-control" accept="image/*" multiple>
                                    
                                    <div id="new-sub-images-preview" class="new-images-preview mt-3 d-none">
                                        <div class="w-100 mb-2 small fw-bold text-success">Newly selected images:</div>
                                        <!-- Previews will appear here -->
                                    </div>

                                    <div id="existing-sub-images" class="mt-3">
                                        <?php 
                                        $sub_images = json_decode($property['images'] ?? '[]', true);
                                        if (is_array($sub_images) && !empty($sub_images)): 
                                            echo '<div class="small fw-bold text-muted mb-2">Current gallery images:</div>';
                                            echo '<div class="d-flex flex-wrap gap-2">';
                                            foreach ($sub_images as $img): ?>
                                                <div class="sub-image-preview">
                                                    <img src="../images/<?php echo htmlspecialchars($img); ?>" alt="Sub image">
                                                    <button type="button" class="remove-img-btn" onclick="removeSubImage(this, '<?php echo $img; ?>')">&times;</button>
                                                </div>
                                            <?php endforeach; 
                                            echo '</div>';
                                        endif; ?>
                                    </div>
                                    <div id="removed-images-container"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <a href="properties-new.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i>Save as Draft
                                </button>
                                <button type="button" class="btn btn-primary px-4" onclick="handlePublishClick()">
                                    <i class="fas fa-save me-2"></i><?php echo $edit_mode ? 'Update Property' : 'Publish Property'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Publish Confirmation Modal -->
    <div class="modal fade" id="publishModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Confirm Publish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you finished and want to publish this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="submitDraft()">No, Save as Draft</button>
                    <button type="button" class="btn btn-primary" onclick="submitPublish()">Yes, Publish</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var currentStatus = '<?php echo $property['status'] ?? 'draft'; ?>';
        
        const districtsByProvince = {
            'Kigali City': ['Gasabo', 'Kicukiro', 'Nyarugenge'],
            'Eastern Province': ['Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'],
            'Northern Province': ['Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'],
            'Southern Province': ['Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'],
            'Western Province': ['Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro']
        };

        const provinceSelect = document.getElementById('provinceSelect');
        const districtSelect = document.getElementById('districtSelect');

        function populateDistricts(province, selectedDistrict = '') {
            districtSelect.innerHTML = '<option value="">Select District</option>';
            if (districtsByProvince[province]) {
                districtsByProvince[province].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    if (district === selectedDistrict) option.selected = true;
                    districtSelect.appendChild(option);
                });
            }
        }

        provinceSelect.addEventListener('change', function() {
            populateDistricts(this.value);
        });

        // Initialize district if province is already selected (e.g., when editing)
        if (provinceSelect.value) {
            const currentDistrict = '<?php echo $property['district'] ?? ''; ?>';
            populateDistricts(provinceSelect.value, currentDistrict);
        }

        function removeSubImage(btn, filename) {
            if (confirm('Are you sure you want to remove this image?')) {
                const container = document.getElementById('removed-images-container');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_sub_images[]';
                input.value = filename;
                container.appendChild(input);
                btn.parentElement.remove();
            }
        }

        function handlePublishClick() {
            var statusSelect = document.getElementById('statusSelect');
            // If current status is draft OR user has 'draft' selected
            if (currentStatus === 'draft' || statusSelect.value === 'draft') {
                var myModal = new bootstrap.Modal(document.getElementById('publishModal'));
                myModal.show();
            } else {
                // Already published and not changing to draft, just submit
                submitPublish();
            }
        }

        function submitDraft() {
            var form = document.querySelector('form');
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'save_draft';
            input.value = '1';
            form.appendChild(input);
            form.submit();
        }

        function submitPublish() {
            var form = document.querySelector('form');
            var statusSelect = document.getElementById('statusSelect');
            
            // If fetching strictly 'draft', assume we want to sell/rent
            // You might want to ask user, or default to For Sale.
            if (statusSelect.value === 'draft') {
                statusSelect.value = 'for-sale'; // Defaulting to For Sale logic
            }
            form.submit();
        }

        // New Images Preview Logic
        document.getElementById('subImagesInput').addEventListener('change', function() {
            const previewContainer = document.getElementById('new-sub-images-preview');
            // Keep the header
            previewContainer.innerHTML = '<div class="w-100 mb-2 small fw-bold text-success">Newly selected images (to be uploaded):</div>';
            
            if (this.files && this.files.length > 0) {
                previewContainer.classList.remove('d-none');
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'new-img-item';
                            div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                            previewContainer.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.classList.add('d-none');
            }
        });
    </script>
    <?php include 'includes/footer.php'; ?>
