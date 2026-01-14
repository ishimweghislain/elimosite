document.addEventListener('DOMContentLoaded', function () {
    // Only show loader if this is a fresh session landing or specifically requested
    // For this task, we'll show it on the main dashboard index.php if not shown recently
    const dashboardIndex = window.location.pathname.endsWith('admin/index.php') || window.location.pathname.endsWith('admin/');

    if (dashboardIndex && !sessionStorage.getItem('admin_welcomed')) {
        createLoader();
        sessionStorage.setItem('admin_welcomed', 'true');
    }
});

function createLoader() {
    const loader = document.createElement('div');
    loader.id = 'admin-loader';
    loader.innerHTML = `
        <div class="stars-container" id="stars"></div>
        <div class="loader-content">
            <img src="../images/white-logo.jpg" class="loader-logo" alt="Elimo">
            <h1 class="loader-text">Welcome back, Elimo Admin!</h1>
            <p class="loader-subtext">Preparing your dashboard...</p>
        </div>
    `;
    document.body.appendChild(loader);

    // Generate Stars
    const starsContainer = document.getElementById('stars');
    for (let i = 0; i < 50; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const size = Math.random() * 4 + 2;
        const duration = Math.random() * 3 + 2;
        star.style.left = `${x}%`;
        star.style.top = `${y}%`;
        star.style.width = `${size}px`;
        star.style.height = `${size}px`;
        star.style.setProperty('--duration', `${duration}s`);
        starsContainer.appendChild(star);
    }

    // Generate Popping Shapes
    setInterval(() => {
        const circle = document.createElement('div');
        circle.className = 'pop-circle';
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const size = Math.random() * 50 + 20;
        circle.style.left = `${x}%`;
        circle.style.top = `${y}%`;
        circle.style.width = `${size}px`;
        circle.style.height = `${size}px`;
        document.getElementById('admin-loader') ? document.getElementById('admin-loader').appendChild(circle) : null;
        setTimeout(() => circle.remove(), 2000);
    }, 400);

    // Hide after 5 seconds
    setTimeout(() => {
        loader.classList.add('fade-out');
        setTimeout(() => loader.remove(), 800);
    }, 5000);
}
