<div class="col-md-3 col-lg-2 p-0 sidebar collapse d-md-block" id="sidebarMenu">
    <div class="sidebar-brand">
        <i class="fas fa-building me-2"></i> ELIMO ADMIN
    </div>
    <div class="d-flex flex-column p-0">
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="properties-new.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'properties-new.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> <span>Properties</span>
            </a>
            <a href="developments.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'developments.php' ? 'active' : ''; ?>">
                <i class="fas fa-hard-hat"></i> <span>Developments</span>
            </a>
            <a href="drafts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'drafts.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> <span>Drafts</span>
            </a>
            <a href="team.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'team.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> <span>Team</span>
            </a>
            <a href="blog.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'blog.php' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i> <span>Blog</span>
            </a>
            <a href="faqs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'faqs.php' ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i> <span>FAQs</span>
            </a>
            <a href="contacts.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'contacts.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> <span>Contacts</span>
            </a>
            <a href="inquiries.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'inquiries.php' ? 'active' : ''; ?>">
                <i class="fas fa-comment-dollar"></i> <span>Inquiries</span>
            </a>
            <a href="subscribers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'subscribers.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-check"></i> <span>Subscribers</span>
            </a>
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> <span>Settings</span>
            </a>
            <div class="mt-4 border-top border-secondary pt-2">
                <a href="#" class="nav-link text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>
</div>
