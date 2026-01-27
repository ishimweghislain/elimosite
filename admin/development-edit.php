<?php
require_once '../includes/config.php';

require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edit_mode = ($id > 0);
$development = $edit_mode ? get_record('developments', $id) : null;

if ($edit_mode && !$development) {
    header('Location: manage-developments.php?error=not_found');
    exit;
}

$error = '';
$success = '';

// Get Districts logic (same as property edit)
$districts_json = json_encode([
    'Kigali City' => ['Gasabo', 'Kicukiro', 'Nyarugenge'],
    'Eastern Province' => ['Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'],
    'Northern Province' => ['Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'],
    'Southern Province' => ['Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'],
    'Western Province' => ['Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro']
]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => clean_input($_POST['title'] ?? ''),
        'location' => clean_input($_POST['location'] ?? ''),
        'province' => clean_input($_POST['province'] ?? ''),
        'district' => clean_input($_POST['district'] ?? ''),
        'description' => clean_input($_POST['description'] ?? ''),
        'about_location' => clean_input($_POST['about_location'] ?? ''),
        'proximity' => clean_input($_POST['proximity'] ?? ''),
        'youtube_url' => clean_input($_POST['youtube_url'] ?? ''),
        'instagram_url' => clean_input($_POST['instagram_url'] ?? ''),
        'features' => isset($_POST['features']) ? json_encode($_POST['features']) : json_encode([]),
        'amenities' => isset($_POST['amenities']) ? json_encode($_POST['amenities']) : json_encode([]),
        'ideal_for' => isset($_POST['ideal_fors']) ? json_encode($_POST['ideal_fors']) : json_encode([])
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['image'], '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024);
        if ($upload_result['success']) {
            $data['image_main'] = $upload_result['filename'];
            // Delete old image if exists
            if ($edit_mode && !empty($development['image_main'])) {
                @unlink('../images/' . $development['image_main']);
            }
        }
    }

    // Handle Gallery (multiple images)
    $existing_images = $edit_mode ? json_decode($development['images'] ?? '[]', true) : [];
    if (!is_array($existing_images)) $existing_images = [];

    // Remove images
    if (isset($_POST['remove_images'])) {
        foreach ($_POST['remove_images'] as $img_to_remove) {
            if (($key = array_search($img_to_remove, $existing_images)) !== false) {
                unset($existing_images[$key]);
                @unlink('../images/' . $img_to_remove);
            }
        }
    }

    // Add newly uploaded images from AJAX
    if (!empty($_POST['uploaded_sub_images'])) {
        $uploaded_images = json_decode($_POST['uploaded_sub_images'], true);
        if (is_array($uploaded_images)) {
            $existing_images = array_merge($existing_images, $uploaded_images);
        }
    }
    
    // Legacy support for direct uploads if still used
    if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
        // ... handled by AJAX now usually, but kept for safety
    }
    $data['images'] = json_encode(array_values($existing_images));

    $result = false;
    if ($edit_mode) {
        $result = update_record('developments', $data, $id);
    } else {
        $result = insert_record('developments', $data);
        if ($result) $id = $result; // result is the new ID for inserts
    }

    if ($result) {
        // Handle Linked Properties
        global $pdo;
        if (isset($_POST['linked_properties'])) {
            $linked_ids = $_POST['linked_properties'];
            // Unlink current ones first to refresh
            $pdo->prepare("UPDATE properties SET development_id = NULL WHERE development_id = ?")->execute([$id]);
            // Link new ones
            if (is_array($linked_ids)) {
                foreach ($linked_ids as $p_id) {
                    $pdo->prepare("UPDATE properties SET development_id = ? WHERE id = ?")->execute([$id, $p_id]);
                }
            }
        } else {
            // Remove all links if none selected
            $pdo->prepare("UPDATE properties SET development_id = NULL WHERE development_id = ?")->execute([$id]);
        }

        header("Location: manage-developments.php?success=" . ($edit_mode ? 'updated' : 'added'));
        exit;
    } else {
        $error = 'Failed to save development.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Development - Elimo Admin</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .visibility-toggle { float: right; margin-top: -30px; }
        .gallery-preview { display: flex; flex-wrap: wrap; gap: 15px; }
        .gallery-item { position: relative; width: 150px; }
        .gallery-item img { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; }
        .gallery-item .remove-btn { position: absolute; top: -10px; right: -10px; border-radius: 50%; padding: 0 6px; }
        .linked-prop-item { border: 1px solid #eee; padding: 10px; border-radius: 8px; margin-bottom: 10px; background: #f9f9f9; display: flex; align-items: center; }
        .linked-prop-item img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4">
                    <h1 class="h2 fw-bold"><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Development</h1>
                    <a href="manage-developments.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary fw-bold">Primary Details</h5></div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Development Title / Name *</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($development['title'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Province *</label>
                                    <select name="province" id="provinceSelect" class="form-select" required onchange="populateDistricts(this.value)">
                                        <option value="">Select Province</option>
                                        <option value="Kigali City" <?php echo ($development['province'] ?? '') === 'Kigali City' ? 'selected' : ''; ?>>Kigali City</option>
                                        <option value="Eastern Province" <?php echo ($development['province'] ?? '') === 'Eastern Province' ? 'selected' : ''; ?>>Eastern Province</option>
                                        <option value="Northern Province" <?php echo ($development['province'] ?? '') === 'Northern Province' ? 'selected' : ''; ?>>Northern Province</option>
                                        <option value="Southern Province" <?php echo ($development['province'] ?? '') === 'Southern Province' ? 'selected' : ''; ?>>Southern Province</option>
                                        <option value="Western Province" <?php echo ($development['province'] ?? '') === 'Western Province' ? 'selected' : ''; ?>>Western Province</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">District *</label>
                                    <select name="district" id="districtSelect" class="form-select" required>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Specific Location *</label>
                                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($development['location'] ?? ''); ?>" required placeholder="e.g. Nyarutarama, near MTN Centre">
                                </div>
                                <div class="col-12 mt-4">
                                    <label class="form-label">Development Description</label>
                                    <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($development['description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary fw-bold">Environment & Location</h5></div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Environmental Highlights</label>
                                    <textarea name="about_location" class="form-control" rows="3" placeholder="Environment details..."><?php echo htmlspecialchars($development['about_location'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ideal For Project</label>
                                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background: #f8f9fa;">
                                        <?php 
                                        $selected_ideals = json_decode($development['ideal_for'] ?? '[]', true);
                                        if (!is_array($selected_ideals)) $selected_ideals = [];
                                        $opts = $pdo->query("SELECT name FROM property_features_master WHERE type = 'ideal_for' AND is_active = 1 ORDER BY name ASC")->fetchAll();
                                        foreach ($opts as $opt): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="ideal_fors[]" value="<?php echo htmlspecialchars($opt['name']); ?>" id="ideal_<?php echo md5($opt['name']); ?>" <?php echo in_array($opt['name'], $selected_ideals) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="ideal_<?php echo md5($opt['name']); ?>">
                                                    <?php echo htmlspecialchars($opt['name']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">In Close Proximity to</label>
                                    <textarea name="proximity" class="form-control" rows="1"><?php echo htmlspecialchars($development['proximity'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features & Amenities Selection -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary fw-bold">Features & Amenities</h5></div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <label class="form-label fw-bold mb-3">Project Features</label>
                                    <div class="row row-cols-1 row-cols-sm-2">
                                        <?php 
                                        $current_features = json_decode($development['features'] ?? '[]', true);
                                        $stmt = $pdo->query("SELECT * FROM property_features_master WHERE type = 'feature' AND is_active = 1 ORDER BY name ASC");
                                        while ($f = $stmt->fetch()): ?>
                                            <div class="col mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="features[]" value="<?php echo $f['name']; ?>" id="f_<?php echo $f['id']; ?>" <?php echo in_array($f['name'], $current_features) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label small" for="f_<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['name']); ?></label>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <label class="form-label fw-bold mb-3">Project Amenities</label>
                                    <div class="row row-cols-1 row-cols-sm-2">
                                        <?php 
                                        $current_amenities = json_decode($development['amenities'] ?? '[]', true);
                                        $stmt = $pdo->query("SELECT * FROM property_features_master WHERE type = 'amenity' AND is_active = 1 ORDER BY name ASC");
                                        while ($a = $stmt->fetch()): ?>
                                            <div class="col mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="<?php echo $a['name']; ?>" id="a_<?php echo $a['id']; ?>" <?php echo in_array($a['name'], $current_amenities) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label small" for="a_<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['name']); ?></label>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media & Listings Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0 text-primary fw-bold">Media & Links</h5></div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Banner Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <?php if ($edit_mode && !empty($development['image_main'])): ?>
                                        <img src="../images/<?php echo $development['image_main']; ?>" class="mt-2 rounded" style="width: 100%; height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gallery Images (Sub-images)</label>
                                    <div class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Use <strong>Ctrl</strong> to select multiple.</div>
                                    <input type="file" id="subImagesInput" class="form-control" multiple accept="image/*">
                                    
                                    <!-- Progress Bar -->
                                    <div id="upload-progress-container" class="mt-3 d-none">
                                        <div class="progress" style="height: 10px;">
                                            <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div id="upload-message" class="small mt-1 text-primary"></div>
                                    </div>

                                    <div id="new-sub-images-preview" class="gallery-preview mt-3 d-none"></div>
                                    <input type="hidden" name="uploaded_sub_images" id="uploadedSubImages" value="">

                                    <div class="gallery-preview mt-3">
                                        <?php 
                                        $imgs = json_decode($development['images'] ?? '[]', true);
                                        foreach ($imgs as $img): ?>
                                            <div class="gallery-item">
                                                <img src="../images/<?php echo $img; ?>">
                                                <button type="button" class="btn btn-sm btn-danger remove-btn" onclick="removeGalleryImg(this, '<?php echo $img; ?>')"><i class="fas fa-times"></i></button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div id="removed-images-container"></div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label class="form-label">YouTube URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-youtube text-danger"></i></span>
                                        <input type="url" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($development['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label class="form-label">Instagram URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-instagram text-primary"></i></span>
                                        <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($development['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Listings Management -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary fw-bold">Development Listings</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#propertyModal">
                                <i class="fas fa-plus me-2"></i>Add Properties
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div id="linked-list-container">
                                <?php 
                                if ($edit_mode) {
                                    $stmt = $pdo->prepare("SELECT id, title, image_main, prop_id FROM properties WHERE development_id = ?");
                                    $stmt->execute([$id]);
                                    while ($p = $stmt->fetch()) {
                                        echo '<div class="linked-prop-item">
                                                <img src="../images/'.($p['image_main'] ?: 'placeholder.jpg').'">
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">'.htmlspecialchars($p['title']).'</div>
                                                    <small class="text-muted">ID: '.htmlspecialchars($p['prop_id']).'</small>
                                                </div>
                                                <input type="hidden" name="linked_properties[]" value="'.$p['id'].'">
                                                <button type="button" class="btn btn-link text-danger" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
                                              </div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                            <i class="fas fa-save me-2"></i>Save & Publish Development
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Properties Selection Modal -->
    <div class="modal fade" id="propertyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Properties to Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="propSearch" class="form-control" placeholder="Search by title or ID...">
                    </div>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover">
                            <tbody>
                                <?php 
                                // Show properties that are either not linked anywhere OR already linked to this development
                                $query = "SELECT id, title, prop_id, image_main FROM properties WHERE (development_id IS NULL OR development_id = 0";
                                if ($edit_mode) $query .= " OR development_id = $id";
                                $query .= ") AND category != 'Developments' ORDER BY title ASC";
                                $all_props = $pdo->query($query)->fetchAll();
                                foreach ($all_props as $p): ?>
                                    <tr class="prop-row" data-search="<?php echo strtolower($p['title'].$p['prop_id']); ?>">
                                        <td><img src="../images/<?php echo $p['image_main'] ?: 'placeholder.jpg'; ?>" style="width:40px; height:40px; object-fit:cover;" class="rounded"></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($p['prop_id']); ?></small>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPropLink(<?php echo $p['id']; ?>, '<?php echo addslashes($p['title']); ?>', '<?php echo $p['image_main'] ?: 'placeholder.jpg'; ?>', '<?php echo $p['prop_id']; ?>')">Select</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const districts = <?php echo $districts_json; ?>;
        function populateDistricts(province, selected = '') {
            const select = document.getElementById('districtSelect');
            select.innerHTML = '<option value="">Select District</option>';
            if (districts[province]) {
                districts[province].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    if (d === selected) opt.selected = true;
                    select.appendChild(opt);
                });
            }
        }

        function addPropLink(id, title, img, propId) {
            const container = document.getElementById('linked-list-container');
            if (container.querySelector(`input[value="${id}"]`)) {
                alert('Property already added'); return;
            }
            const div = document.createElement('div');
            div.className = 'linked-prop-item';
            div.innerHTML = `
                <img src="../images/${img}">
                <div class="flex-grow-1">
                    <div class="fw-bold">${title}</div>
                    <small class="text-muted">ID: ${propId}</small>
                </div>
                <input type="hidden" name="linked_properties[]" value="${id}">
                <button type="button" class="btn btn-link text-danger" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(div);
        }

        document.getElementById('propSearch').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.prop-row').forEach(row => {
                row.style.display = row.getAttribute('data-search').includes(val) ? '' : 'none';
            });
        });

        // AJAX Image Upload System
        let uploadedImages = [];
        let isUploading = false;

        document.getElementById('subImagesInput').addEventListener('change', function() {
            const files = this.files;
            if (files && files.length > 0) {
                showImagePreviews(files);
                uploadImagesAjax(files);
            }
        });

        function showImagePreviews(files) {
            const container = document.getElementById('new-sub-images-preview');
            container.innerHTML = '';
            container.classList.remove('d-none');
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'gallery-item';
                    div.innerHTML = `<img src="${e.target.result}">`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function uploadImagesAjax(files) {
            isUploading = true;
            const progressContainer = document.getElementById('upload-progress-container');
            const progressBar = document.getElementById('upload-progress-bar');
            const uploadMessage = document.getElementById('upload-message');
            progressContainer.classList.remove('d-none');
            
            const formData = new FormData();
            Array.from(files).forEach(file => formData.append('images[]', file));

            fetch('ajax/upload-images.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.uploaded_files.forEach(f => uploadedImages.push(f.saved_name));
                    document.getElementById('uploadedSubImages').value = JSON.stringify(uploadedImages);
                    progressBar.style.width = '100%';
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    uploadMessage.textContent = '✓ Uploaded successfully';
                }
                isUploading = false;
            });
        }

        function removeGalleryImg(btn, filename) {
            if (confirm('Remove image?')) {
                const container = document.getElementById('removed-images-container');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'remove_images[]';
                input.value = filename;
                container.appendChild(input);
                btn.parentElement.remove();
            }
        }

        // YouTube Preview Logic
        const youtubeInput = document.querySelector('input[name="youtube_url"]');
        const youtubePreviewContainer = document.createElement('div');
        youtubePreviewContainer.id = 'youtube-preview';
        youtubePreviewContainer.className = 'mt-3';
        youtubeInput.parentElement.parentElement.appendChild(youtubePreviewContainer);
        
        if (youtubeInput.value) updateYoutubePreview(youtubeInput.value);
        youtubeInput.addEventListener('input', function() { updateYoutubePreview(this.value); });

        function updateYoutubePreview(url) {
            const videoId = extractYoutubeId(url);
            if (videoId) {
                youtubePreviewContainer.innerHTML = `<div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm" style="max-width: 400px;"><iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe></div>`;
            } else {
                youtubePreviewContainer.innerHTML = '';
            }
        }

        function extractYoutubeId(url) {
             if (!url) return null;
             var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
             var match = url.match(regExp);
             if (match && match[2].length == 11) return match[2];
             return null;
        }

        // Init districts
        const currentProvince = "<?php echo $development['province'] ?? ''; ?>";
        const currentDistrict = "<?php echo $development['district'] ?? ''; ?>";
        if (currentProvince) populateDistricts(currentProvince, currentDistrict);
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
