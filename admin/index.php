<?php
require_once '../includes/config.php';
require_admin();

// Get dashboard statistics
$stats = get_property_stats();
$total_contacts = count_records('contact_messages', ['status' => 'new']);
$total_inquiries = count_records('property_inquiries', ['status' => 'new']);
$total_subscribers = count_records('newsletter_subscribers', ['is_active' => 1]);
$recent_properties = get_recent_properties(5);
$recent_contacts = get_records('contact_messages', [], 'created_at DESC', 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo get_setting('site_name'); ?></title>
    <?php include 'includes/header.php'; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h1 class="h2 fw-bold text-dark">Dashboard</h1>
                            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                        </div>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="../index.php" target="_blank" class="btn btn-outline-primary shadow-sm" style="background: white;">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4 fade-in">
                    <!-- ... stats ... -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Properties
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['total_properties']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-home stat-icon text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Inquiries
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $total_inquiries; ?>
                                        </div>
                                        <small class="text-muted">New Inquiries</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-comment-dollar stat-icon text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Contacts
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $total_contacts; ?>
                                        </div>
                                        <small class="text-muted">Unread Messages</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope stat-icon text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Subscribers
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $total_subscribers; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users stat-icon text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Properties -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Properties</h6>
                                <a href="properties-new.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recent_properties)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_properties as $property): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <?php if (!empty($property['image_main'])): ?>
                                                                    <img src="../images/<?php echo htmlspecialchars($property['image_main']); ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <i class="fas fa-home text-secondary"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <a href="properties-new.php?edit=<?php echo $property['id']; ?>" class="text-dark text-decoration-none fw-bold">
                                                                        <?php echo htmlspecialchars($property['title']); ?>
                                                                    </a>
                                                                    <div class="small text-muted"><?php echo htmlspecialchars($property['property_type']); ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo format_price($property['price']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                echo match($property['status']) {
                                                                    'for-rent' => 'info',
                                                                    'for-sale' => 'success',
                                                                    'under-construction' => 'warning',
                                                                    'sold' => 'danger',
                                                                    default => 'secondary'
                                                                };
                                                            ?>">
                                                                <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $property['status']))); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-home fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">No properties added yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Contact Messages -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Messages</h6>
                                <a href="contacts.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recent_contacts)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_contacts as $contact): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="bg-light rounded-circle p-2 me-2">
                                                                    <i class="fas fa-user text-secondary"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></div>
                                                                    <div class="small text-muted"><?php echo htmlspecialchars($contact['email']); ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="small"><?php echo format_date($contact['created_at'], 'M d'); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $contact['status'] === 'new' ? 'danger' : 'secondary'; ?>">
                                                                <?php echo htmlspecialchars(ucfirst($contact['status'])); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-envelope fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">No messages yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
