<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../includes/config.php';
require_login();

// Redirect non-admins away from dashboard
if (!is_admin()) {
    header('Location: properties-new.php');
    exit;
}

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
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .main-content {
            padding-top: 2rem;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }

        .dashboard-header:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-2px);
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--card-shadow);
            cursor: pointer;
            position: relative;
            background: white;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            transition: height 0.3s ease;
        }

        .stat-card.primary::before {
            background: var(--primary-gradient);
        }

        .stat-card.success::before {
            background: var(--success-gradient);
        }

        .stat-card.info::before {
            background: var(--info-gradient);
        }

        .stat-card.warning::before {
            background: var(--warning-gradient);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--card-hover-shadow);
        }

        .stat-card:hover::before {
            height: 100%;
            opacity: 0.05;
        }

        .stat-card .card-body {
            padding: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.15;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            opacity: 0.25;
            transform: scale(1.1) rotate(5deg);
        }

        .stat-card .h5 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card .text-xs {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .content-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }

        .content-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-4px);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1.5rem;
            color: white;
        }

        .card-header h6 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
            transform: scale(1.01);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .fade-in {
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .stat-card:nth-child(1) {
            animation: slideInLeft 0.5s ease;
        }

        .stat-card:nth-child(2) {
            animation: slideInLeft 0.6s ease;
        }

        .stat-card:nth-child(3) {
            animation: slideInLeft 0.7s ease;
        }

        .stat-card:nth-child(4) {
            animation: slideInLeft 0.8s ease;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .rounded-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rounded-circle i {
            color: white !important;
        }

        .table thead th {
            border-bottom: 2px solid #e8eef5;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        img.rounded {
            border-radius: 12px !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .welcome-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }

            .dashboard-header {
                padding: 1.5rem;
            }

            .stat-card .h5 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary d-md-none me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div>
                                <h1 class="h2 fw-bold mb-2" style="color: #1e293b;">Dashboard Overview</h1>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-wave-square me-2"></i>
                                    Welcome back, <span class="welcome-text"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!
                                </p>
                            </div>
                        </div>
                        
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <a href="../index.php" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i>View Website
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4 fade-in">
                    <!-- Properties Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="properties-new.php" class="text-decoration-none">
                            <div class="card stat-card primary h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs text-primary mb-2">
                                                Total Properties
                                            </div>
                                            <div class="h5">
                                                <?php echo $stats['total_properties']; ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-up text-success me-1"></i>
                                                View all listings
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-home stat-icon text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Inquiries Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="inquiries.php" class="text-decoration-none">
                            <div class="card stat-card success h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs text-success mb-2">
                                                New Inquiries
                                            </div>
                                            <div class="h5">
                                                <?php echo $total_inquiries; ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php if ($total_inquiries > 0): ?>
                                                    <span class="pulse">
                                                        <i class="fas fa-circle text-danger me-1" style="font-size: 0.5rem;"></i>
                                                    </span>
                                                    Requires attention
                                                <?php else: ?>
                                                    <i class="fas fa-check text-success me-1"></i>
                                                    All caught up
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comment-dollar stat-icon text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Contacts Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="contacts.php" class="text-decoration-none">
                            <div class="card stat-card info h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs text-info mb-2">
                                                Unread Messages
                                            </div>
                                            <div class="h5">
                                                <?php echo $total_contacts; ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php if ($total_contacts > 0): ?>
                                                    <span class="pulse">
                                                        <i class="fas fa-circle text-danger me-1" style="font-size: 0.5rem;"></i>
                                                    </span>
                                                    New messages
                                                <?php else: ?>
                                                    <i class="fas fa-check text-success me-1"></i>
                                                    No new messages
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope stat-icon text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Subscribers Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="subscribers.php" class="text-decoration-none">
                            <div class="card stat-card warning h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs text-warning mb-2">
                                                Active Subscribers
                                            </div>
                                            <div class="h5">
                                                <?php echo $total_subscribers; ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-chart-line text-success me-1"></i>
                                                Newsletter list
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users stat-icon text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Properties -->
                    <div class="col-lg-6 mb-4">
                        <div class="card content-card shadow mb-4">
                            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0">
                                    <i class="fas fa-home me-2"></i>
                                    Recent Properties
                                </h6>
                                <a href="properties-new.php" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border-radius: 8px;">
                                    View All <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recent_properties)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
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
                                                                    <img src="../images/<?php echo htmlspecialchars($property['image_main']); ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                        <i class="fas fa-home text-secondary"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div>
                                                                    <a href="properties-new.php?edit=<?php echo $property['id']; ?>" class="text-dark text-decoration-none fw-bold d-block">
                                                                        <?php echo htmlspecialchars($property['title']); ?>
                                                                    </a>
                                                                    <div class="small text-muted">
                                                                        <i class="fas fa-tag me-1"></i>
                                                                        <?php echo htmlspecialchars($property['property_type']); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold" style="color: #1e293b;">
                                                                <?php echo format_price($property['price']); ?>
                                                            </span>
                                                        </td>
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
                                    <div class="text-center empty-state">
                                        <i class="fas fa-home fa-4x"></i>
                                        <p class="text-muted mt-3 mb-0">No properties added yet.</p>
                                        <a href="properties-new.php" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus me-2"></i>Add Your First Property
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Contact Messages -->
                    <div class="col-lg-6 mb-4">
                        <div class="card content-card shadow mb-4">
                            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0">
                                    <i class="fas fa-comments me-2"></i>
                                    Recent Messages
                                </h6>
                                <a href="contacts.php" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border-radius: 8px;">
                                    View All <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($recent_contacts)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
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
                                                                <div class="rounded-circle me-3">
                                                                    <i class="fas fa-user"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></div>
                                                                    <div class="small text-muted">
                                                                        <i class="fas fa-envelope me-1"></i>
                                                                        <?php echo htmlspecialchars($contact['email']); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="small text-muted">
                                                                <i class="far fa-clock me-1"></i>
                                                                <?php echo format_date($contact['created_at'], 'M d'); ?>
                                                            </span>
                                                        </td>
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
                                    <div class="text-center empty-state">
                                        <i class="fas fa-envelope fa-4x"></i>
                                        <p class="text-muted mt-3 mb-0">No messages yet.</p>
                                        <p class="small text-muted">Contact messages will appear here</p>
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
</body>
</html>