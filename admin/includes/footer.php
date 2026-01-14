    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
