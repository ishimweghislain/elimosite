<?php
require_once '../includes/config.php';

require_admin();

$blog_post = null;
$edit_mode = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $blog_post = get_record('blog_posts', $id);
    $edit_mode = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = isset($_POST['save_draft']) ? 'draft' : clean_input($_POST['status'] ?? 'draft');

    $data = [
        'title' => clean_input($_POST['title'] ?? ''),
        'slug' => create_slug(clean_input($_POST['title'] ?? '')),
        'category' => clean_input($_POST['category'] ?? 'creative'),
        'excerpt' => clean_input($_POST['excerpt'] ?? ''),
        'content' => $_POST['content'] ?? '', // Don't clean HTML content
        'status' => $status,
        'youtube_url' => clean_input($_POST['youtube_url'] ?? ''),
        'instagram_url' => clean_input($_POST['instagram_url'] ?? '')
    ];

    // Video upload removed - using YouTube URL only

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['image'], '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 2 * 1024 * 1024); // 2MB
        if ($upload_result['success']) {
            $data['image'] = $upload_result['filename'];
        }
    }

    // Handle sub-images upload
    $current_sub_images = [];
    if ($edit_mode && !empty($blog_post['images'])) {
        $current_sub_images = json_decode($blog_post['images'], true);
        if (!is_array($current_sub_images)) $current_sub_images = [];
    }

    // Check if any sub-images were marked for deletion
    if (isset($_POST['remove_sub_images']) && is_array($_POST['remove_sub_images'])) {
        foreach ($_POST['remove_sub_images'] as $img_to_remove) {
            if (($key = array_search($img_to_remove, $current_sub_images)) !== false) {
                unset($current_sub_images[$key]);
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
        // Update existing blog post
        $result = update_record('blog_posts', $data, $id);
        $message = 'updated';
    } else {
        // Add new blog post
        $result = insert_record('blog_posts', $data);
        $message = 'added';
    }

    if ($result) {
        $redirect_url = ($data['status'] === 'draft') ? 'drafts.php' : 'blog.php';
        header('Location: ' . $redirect_url . '?success=' . $message);
        exit;
    } else {
        $error = 'Failed to save blog post. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Blog Post - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
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
            width: 80px;
            height: 80px;
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
        .note-editor {
            border-color: #e3e6f0 !important;
            box-shadow: none !important;
        }
        .note-toolbar {
            background-color: #f8f9fc !important;
            border-bottom: 1px solid #e3e6f0 !important;
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
                        <h1 class="h2 fw-bold text-dark mb-0"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Blog Post</h1>
                    </div>
                    <a href="blog.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Blog
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger fade-in"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <h5 class="mb-4 text-primary fw-bold">Post Details</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-8">
                                    <label class="form-label">Title *</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog_post['title'] ?? ''); ?>" required placeholder="Enter post title">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Category *</label>
                                    <select name="category" class="form-select">
                                        <option value="creative" <?php echo ($blog_post['category'] ?? '') === 'creative' ? 'selected' : ''; ?>>Creative</option>
                                        <option value="rental" <?php echo ($blog_post['category'] ?? '') === 'rental' ? 'selected' : ''; ?>>Rental</option>
                                        <option value="news" <?php echo ($blog_post['category'] ?? '') === 'news' ? 'selected' : ''; ?>>News</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Excerpt / Summary</label>
                                <textarea name="excerpt" class="form-control" rows="2" placeholder="Brief summary for listing pages..."><?php echo htmlspecialchars($blog_post['excerpt'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Content *</label>
                                <textarea name="content" id="content" class="form-control" required><?php echo htmlspecialchars($blog_post['content'] ?? ''); ?></textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Publish Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" <?php echo ($blog_post['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo ($blog_post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Featured Main Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <?php if ($edit_mode && !empty($blog_post['image'])): ?>
                                        <div class="mt-2">
                                            <img src="../images/<?php echo htmlspecialchars($blog_post['image']); ?>" class="preview-img" alt="Current image">
                                            <div class="small text-muted mt-1">Current Image</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Other Gallery Images (Sub-images)</label>
                                    <div class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Hold <strong>Ctrl</strong> to select multiple images.</div>
                                    <input type="file" name="sub_images[]" id="subImagesInput" class="form-control" accept="image/*" multiple>
                                    
                                    <div id="new-sub-images-preview" class="new-images-preview mt-3 d-none">
                                        <div class="w-100 mb-2 small fw-bold text-success">Newly selected images:</div>
                                    </div>

                                    <div id="sub-images-preview" class="mt-3">
                                        <?php 
                                        $sub_images = json_decode($blog_post['images'] ?? '[]', true);
                                        if (is_array($sub_images) && !empty($sub_images)): 
                                            foreach ($sub_images as $img): ?>
                                                <div class="sub-image-preview">
                                                    <img src="../images/<?php echo htmlspecialchars($img); ?>" alt="Sub image">
                                                    <button type="button" class="remove-img-btn" onclick="removeSubImage(this, '<?php echo $img; ?>')">&times;</button>
                                                </div>
                                            <?php endforeach; 
                                        endif; ?>
                                    </div>
                                    <div id="removed-images-container"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">YouTube URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-youtube text-danger"></i></span>
                                        <input type="url" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($blog_post['youtube_url'] ?? ''); ?>" placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Paste the full YouTube video URL</small>
                                    <div id="youtube-preview" class="mt-3"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Instagram URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-instagram text-primary"></i></span>
                                        <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($blog_post['instagram_url'] ?? ''); ?>" placeholder="https://www.instagram.com/reels/...">
                                    </div>
                                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> Paste the full Instagram post or reel URL</small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <a href="blog.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i>Save as Draft
                                </button>
                                <button type="button" class="btn btn-primary px-4" onclick="handlePublishClick()">
                                    <i class="fas fa-save me-2"></i><?php echo $edit_mode ? 'Update Post' : 'Publish Post'; ?>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // YouTube Preview Logic
            const youtubeInput = document.querySelector('input[name="youtube_url"]');
            const youtubePreviewContainer = document.getElementById('youtube-preview');
            
            if (youtubeInput.value) {
                updateYoutubePreview(youtubeInput.value);
            }
            
            youtubeInput.addEventListener('input', function() {
                updateYoutubePreview(this.value);
            });

            function updateYoutubePreview(url) {
                const videoId = extractYoutubeId(url);
                if (videoId) {
                    youtubePreviewContainer.innerHTML = `
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm" style="max-width: 100%;">
                            <iframe src="https://www.youtube.com/embed/${videoId}" title="YouTube video preview" allowfullscreen></iframe>
                        </div>
                    `;
                } else {
                    youtubePreviewContainer.innerHTML = '';
                }
            }

            function extractYoutubeId(url) {
                 if (!url) return null;
                 var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                 var match = url.match(regExp);
                 if (match && match[2].length == 11) {
                     return match[2];
                 }
                 return null;
            }
        });

        var currentStatus = '<?php echo $blog_post['status'] ?? 'draft'; ?>';

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
            // If currently draft, confirm publish
            if (currentStatus === 'draft') {
                var myModal = new bootstrap.Modal(document.getElementById('publishModal'));
                myModal.show();
            } else {
                // Already published, just save updates
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
            var statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect && statusSelect.value === 'draft') {
                statusSelect.value = 'published';
            }
            form.submit();
        }

        // New Images Preview Logic
        document.getElementById('subImagesInput').addEventListener('change', function() {
            const previewContainer = document.getElementById('new-sub-images-preview');
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
