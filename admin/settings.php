<?php
require_once '../includes/config.php';

require_admin();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            // Update each setting
            $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        header('Location: settings.php?success=updated');
        exit;
    }
}

// Get all settings
$settings = get_records('site_settings', 'ORDER BY id ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Site Settings</h1>
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        Settings updated successfully!
                    </div>
                <?php endif; ?>

                <div class="card shadow mb-4 fade-in">
                    <div class="card-body p-4">
                        <form method="POST">
                            <?php foreach ($settings as $setting): ?>
                                <div class="mb-4">
                                    <label for="<?php echo $setting['setting_key']; ?>" class="form-label fw-bold">
                                        <?php echo htmlspecialchars($setting['description']); ?>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="<?php echo $setting['setting_key']; ?>" 
                                           name="settings[<?php echo $setting['setting_key']; ?>]" 
                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                           required>
                                    <div class="form-text text-muted">Key: <?php echo htmlspecialchars($setting['setting_key']); ?></div>
                                </div>
                            <?php endforeach; ?>

                            <div class="mt-4 pt-3 border-top">
                                <input type="hidden" name="update_settings" value="1">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>
