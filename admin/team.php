<?php
require_once '../includes/config.php';

require_admin();

// Handle team member operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_team'])) {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            delete_record('team_members', $id);
            header('Location: team.php?success=deleted');
            exit;
        }
    }
}

// Get team members (exclude drafts/inactive)
$team_members = get_records('team_members', "WHERE is_active = 1 ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Team Management</h1>
                    </div>
                    <a href="team-edit.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add Team Member
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <?php
                        switch ($_GET['success']) {
                            case 'added': echo 'Team member added successfully!'; break;
                            case 'updated': echo 'Team member updated successfully!'; break;
                            case 'deleted': echo 'Team member deleted successfully!'; break;
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
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Contact Info</th>
                                        <th>Properties</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($team_members)): ?>
                                        <?php foreach ($team_members as $member): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($member['image'])): ?>
                                                        <img src="../images/<?php echo htmlspecialchars($member['image']); ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                                    <?php else: ?>
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($member['name']); ?>&background=4e73df&color=fff" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($member['name']); ?></div>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($member['position']); ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($member['position']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small><i class="fas fa-envelope me-2 text-muted"></i><?php echo htmlspecialchars($member['email']); ?></small>
                                                        <small><i class="fas fa-phone me-2 text-muted"></i><?php echo htmlspecialchars($member['phone']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info rounded-pill"><?php echo $member['listed_properties']; ?> Listings</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="team-edit.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                                            <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                                            <input type="hidden" name="delete_team" value="1">
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
                                                    <i class="fas fa-users fa-4x mb-3"></i>
                                                    <h5>No team members added yet</h5>
                                                    <p>Add your first team member to get started.</p>
                                                </div>
                                                <a href="team-edit.php" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Team Member
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
</html>
