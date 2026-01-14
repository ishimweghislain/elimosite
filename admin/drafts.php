<?php
require_once '../includes/config.php';
require_admin();

// Handle Delete Draft
if (isset($_GET['delete']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];
    $table = '';
    
    if ($type === 'property') $table = 'properties';
    elseif ($type === 'blog') $table = 'blog_posts';
    elseif ($type === 'team') $table = 'team_members';
    
    if ($table) {
        delete_record($table, $id);
        header('Location: drafts.php?deleted=1');
        exit;
    }
}

// Get Drafts
$draft_properties = get_records('properties', ['status' => 'draft'], 'updated_at DESC');
$draft_blogs = get_records('blog_posts', ['status' => 'draft'], 'updated_at DESC');
$draft_team = get_records('team_members', ['is_active' => 0], 'updated_at DESC');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drafts & Pending Items - Admin Panel</title>
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
                        <h1 class="h2 fw-bold text-dark mb-0">Drafts & Pending Items</h1>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-4" id="draftTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="props-tab" data-bs-toggle="tab" data-bs-target="#props" type="button" role="tab" aria-selected="true">
                            Properties <span class="badge bg-secondary ms-1"><?php echo count($draft_properties); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="blogs-tab" data-bs-toggle="tab" data-bs-target="#blogs" type="button" role="tab" aria-selected="false">
                            Blog Posts <span class="badge bg-secondary ms-1"><?php echo count($draft_blogs); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab" aria-selected="false">
                            Team Members <span class="badge bg-secondary ms-1"><?php echo count($draft_team); ?></span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="draftTabsContent">
                    <!-- Properties Tab -->
                    <div class="tab-pane fade show active" id="props" role="tabpanel">
                        <?php if (!empty($draft_properties)): ?>
                            <div class="card shadow mb-4">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr><th>Title</th><th>Category</th><th>Last Updated</th><th>Actions</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($draft_properties as $p): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($p['title']); ?></div>
                                                            <small class="text-muted">ID: <?php echo $p['id']; ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                                                        <td><?php echo format_date($p['updated_at']); ?></td>
                                                        <td>
                                                            <a href="property-edit-new.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">Continue Editing</a>
                                                            <a href="drafts.php?delete=1&type=property&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this draft?')">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No draft properties found.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Blogs Tab -->
                    <div class="tab-pane fade" id="blogs" role="tabpanel">
                        <?php if (!empty($draft_blogs)): ?>
                            <div class="card shadow mb-4">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr><th>Title</th><th>Category</th><th>Last Updated</th><th>Actions</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($draft_blogs as $b): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($b['title']); ?></div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($b['category']); ?></td>
                                                        <td><?php echo format_date($b['updated_at']); ?></td>
                                                        <td>
                                                            <a href="blog-edit.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary">Continue Editing</a>
                                                            <a href="drafts.php?delete=1&type=blog&id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this blog draft?')">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No draft blog posts found.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Team Tab -->
                    <div class="tab-pane fade" id="team" role="tabpanel">
                        <?php if (!empty($draft_team)): ?>
                            <div class="card shadow mb-4">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr><th>Name</th><th>Position</th><th>Last Updated</th><th>Actions</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($draft_team as $t): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($t['name']); ?></div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($t['position']); ?></td>
                                                        <td><?php echo format_date($t['updated_at']); ?></td>
                                                        <td>
                                                            <a href="team-edit.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-primary">Edit & Publish</a>
                                                            <a href="drafts.php?delete=1&type=team&id=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this draft?')">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No inactive team members found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>
