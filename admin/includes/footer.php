    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/toasts.js"></script>
    <?php output_session_toast(); ?>
    <script src="js/loader.js"></script>
    <script>
        // Toggle Sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.querySelector('[data-bs-target="#sidebarMenu"]');
            var sidebar = document.getElementById('sidebarMenu');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    } else {
                        sidebar.classList.add('show');
                    }
                });
            }
        });
    </script>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="logoutModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Logout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-sign-out-alt fa-3x text-danger mb-3 opacity-25"></i>
                    <h5 class="fw-bold">Are you sure you want to leave?</h5>
                    <p class="text-muted">You will need to login again to access the admin panel.</p>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Stay here</button>
                    <a href="../logout.php" class="btn btn-danger px-4">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
