<header class="top-navbar">
    <div class="navbar-left">
        <h1 class="page-title"><?php echo $_SESSION['username'] ?? 'User'; ?> Dashboard</h1>
    </div>
    <div class="navbar-right">
        <!-- Toggle Button -->
        <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>

        <button class="notification-icon">
            <i class="bi bi-bell"></i>
            <span class="notification-badge">3</span>
        </button>
        <div class="user-dropdown">
            <img src="https://picsum.photos/seed/user123/40/40.jpg" alt="User Avatar" class="user-avatar">
        </div>
    </div>
</header>

<script>
    // Update icon based on current theme
    document.addEventListener('DOMContentLoaded', () => {
        const themeIcon = document.getElementById('themeIcon');
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
    });

    // Update icon when theme changes
    function updateThemeIcon() {
        const themeIcon = document.getElementById('themeIcon');
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'dark') {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
    }

    // Modified toggle function to also update icon
    function toggleTheme() {
        const body = document.body;
        const currentTheme = localStorage.getItem('theme') || 'light';

        if (currentTheme === 'light') {
            body.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            body.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        }

        // Update icon after toggling
        updateThemeIcon();
    }
</script>