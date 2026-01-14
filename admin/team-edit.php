<?php
require_once '../includes/config.php';

require_admin();

$team_member = null;
$edit_mode = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $team_member = get_record('team_members', $id);
    $edit_mode = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_active = isset($_POST['save_draft']) ? 0 : 1;

    $social_links = [
        'twitter' => clean_input($_POST['twitter'] ?? ''),
        'facebook' => clean_input($_POST['facebook'] ?? ''),
        'linkedin' => clean_input($_POST['linkedin'] ?? '')
    ];

    $data = [
        'name' => clean_input($_POST['name'] ?? ''),
        'position' => clean_input($_POST['position'] ?? ''),
        'email' => clean_input($_POST['email'] ?? ''),
        'phone' => clean_input($_POST['phone'] ?? ''),
        'bio' => clean_input($_POST['bio'] ?? ''),
        'social_links' => json_encode($social_links),
        'is_active' => $is_active
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['image'], '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 2 * 1024 * 1024); // 2MB
        if ($upload_result['success']) {
            $data['image'] = $upload_result['filename'];
        }
    }

    if ($edit_mode) {
        // Update existing team member
        $result = update_record('team_members', $data, $id);
        $message = 'updated';
    } else {
        // Add new team member
        // Ensure is_active is set
        $result = insert_record('team_members', $data);
        $message = 'added';
    }

    if ($result) {
        $redirect_url = ($data['is_active'] == 0) ? 'drafts.php' : 'team.php';
        header('Location: ' . $redirect_url . '?success=' . $message);
        exit;
    } else {
        $error = 'Failed to save team member. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Team Member - Admin Panel</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .preview-img {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-top: 0.5rem;
            border: 2px solid #e3e6f0;
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
                        <h1 class="h2 fw-bold text-dark mb-0"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Team Member</h1>
                    </div>
                    <a href="team.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Team
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger fade-in"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <h5 class="mb-4 text-primary fw-bold">Personal Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($team_member['name'] ?? ''); ?>" required placeholder="e.g. John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Position *</label>
                                    <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($team_member['position'] ?? ''); ?>" required placeholder="e.g. Senior Agent">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($team_member['email'] ?? ''); ?>" required placeholder="name@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($team_member['phone'] ?? ''); ?>" placeholder="+250 789 517 737">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Bio / Description</label>
                                <textarea name="bio" class="form-control" rows="4" placeholder="Brief biography of the team member..."><?php echo htmlspecialchars($team_member['bio'] ?? ''); ?></textarea>
                            </div>

                            <?php 
                            $social = json_decode($team_member['social_links'] ?? '{}', true);
                            ?>
                            <h5 class="mb-3 text-primary fw-bold mt-4">Social Media Profiles</h5>
                            <div class="row g-3 mb-4 text-muted small mb-2"><i class="fas fa-info-circle me-1"></i> Enter full URLs (e.g., https://facebook.com/username)</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Facebook Profile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary"><i class="fab fa-facebook-f"></i></span>
                                        <input type="url" name="facebook" class="form-control" value="<?php echo htmlspecialchars($social['facebook'] ?? ''); ?>" placeholder="Facebook URL">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Twitter/X Profile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-info"><i class="fab fa-twitter"></i></span>
                                        <input type="url" name="twitter" class="form-control" value="<?php echo htmlspecialchars($social['twitter'] ?? ''); ?>" placeholder="Twitter URL">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">LinkedIn Profile</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary"><i class="fab fa-linkedin-in"></i></span>
                                        <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($social['linkedin'] ?? ''); ?>" placeholder="LinkedIn URL">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <?php if ($edit_mode && !empty($team_member['image'])): ?>
                                        <div class="mt-2 text-center">
                                            <img src="../images/<?php echo htmlspecialchars($team_member['image']); ?>" class="preview-img mb-2" alt="Current photo">
                                            <div class="text-muted small">Current Photo</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-5">
                                <a href="team.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i>Save as Draft
                                </button>
                                <button type="button" class="btn btn-primary px-4" onclick="handlePublishClick()">
                                    <i class="fas fa-save me-2"></i><?php echo $edit_mode ? 'Update Member' : 'Publish Member'; ?>
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
        var isActive = <?php echo $team_member['is_active'] ?? 0; ?>;
        
        function handlePublishClick() {
            // If currently inactive (draft), confirm publish
            if (isActive == 0) {
                var myModal = new bootstrap.Modal(document.getElementById('publishModal'));
                myModal.show();
            } else {
                // Already active, just save updates
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
            // Ensure no save_draft input exists if we want to publish
            // The absence of save_draft implies is_active=1 in PHP logic
            form.submit();
        }
    </script>
    <?php include 'includes/footer.php'; ?>
