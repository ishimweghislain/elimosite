<style>
    :root {
        --sidebar-bg: #1e293b;
        --sidebar-hover: #334155;
        --sidebar-active: #475569;
        --nav-text: #94a3b8;
        --nav-text-active: #ffffff;
    }

    .sidebar {
        background: var(--sidebar-bg);
        min-height: 100vh;
        max-height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        width: 220px;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .sidebar-brand {
        padding: 1.5rem 1rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.2);
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .sidebar-brand:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    .sidebar-brand i {
        font-size: 1.2rem;
        color: #60a5fa;
    }

    .sidebar .nav {
        padding: 0.5rem 0;
    }

    .sidebar .nav-link {
        color: var(--nav-text);
        padding: 0.75rem 1rem;
        margin: 0.2rem 0.5rem;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        font-weight: 500;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
    }

    .sidebar .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: #60a5fa;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .sidebar .nav-link:hover {
        background: var(--sidebar-hover);
        color: white;
        transform: translateX(3px);
    }

    .sidebar .nav-link:hover::before {
        transform: scaleY(1);
    }

    .sidebar .nav-link.active {
        background: var(--sidebar-active);
        color: var(--nav-text-active);
        font-weight: 600;
        transform: translateX(3px);
    }

    .sidebar .nav-link.active::before {
        transform: scaleY(1);
    }

    .sidebar .nav-link i {
        width: 20px;
        margin-right: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover i,
    .sidebar .nav-link.active i {
        color: #60a5fa;
    }

    .sidebar .nav-link span {
        transition: all 0.3s ease;
    }

    .sidebar .border-top {
        border-color: rgba(255, 255, 255, 0.1) !important;
        margin: 0.5rem 0.5rem 0;
    }

    .sidebar .nav-link.text-danger {
        color: var(--nav-text) !important;
    }

    .sidebar .nav-link.text-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #fca5a5 !important;
    }

    .sidebar .nav-link.text-danger:hover i {
        color: #ef4444;
    }

    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .sidebar {
            width: 100%;
            position: relative;
        }

        .sidebar.collapse:not(.show) {
            display: none;
        }

        .sidebar.collapse.show {
            display: block;
        }
    }

    /* Scrollbar Styling */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Adjust main content to account for smaller sidebar */
    .main-content {
        margin-left: 220px;
    }

    @media (max-width: 767.98px) {
        .main-content {
            margin-left: 0;
        }
    }
</style>

<div class="col-md-3 col-lg-2 p-0 sidebar collapse d-md-block" id="sidebarMenu">
    <div class="sidebar-brand">
        <i class="fas fa-building me-2"></i>ELIMO
    </div>
    <div class="d-flex flex-column p-0">
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> 
                <span>Dashboard</span>
            </a>
            <a href="properties-new.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'properties-new.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> 
                <span>Properties</span>
            </a>
            <a href="developments.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'developments.php' ? 'active' : ''; ?>">
                <i class="fas fa-hard-hat"></i> 
                <span>Developments</span>
            </a>
            <a href="drafts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'drafts.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> 
                <span>Drafts</span>
            </a>
            <a href="team.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'team.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> 
                <span>Team</span>
            </a>
            <a href="blog.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'blog.php' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i> 
                <span>Blog</span>
            </a>
            <a href="faqs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'faqs.php' ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i> 
                <span>FAQs</span>
            </a>
            <a href="contacts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'contacts.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> 
                <span>Contacts</span>
            </a>
            <a href="inquiries.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'inquiries.php' ? 'active' : ''; ?>">
                <i class="fas fa-comment-dollar"></i> 
                <span>Inquiries</span>
            </a>
            <a href="subscribers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'subscribers.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-check"></i> 
                <span>Subscribers</span>
            </a>
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> 
                <span>Settings</span>
            </a>
            <div class="mt-3 border-top border-secondary pt-2">
                <a href="#" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
</div>