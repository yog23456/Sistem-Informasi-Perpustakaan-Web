<footer>
        <div class="footer clearfix mb-0 text-muted container-fluid mt-auto py-3">
            <div class="float-start">
                <p>2026 &copy; PerpusPanel - Yogi Saputra</p>
            </div>
        </div>
    </footer>
</div> </div> <script src="<?= base_url('assets/'.$THEME.'/assets/static/js/components/dark.js') ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/assets/compiled/js/app.js') ?>"></script>

<script>
    // Logic Dark Mode manual untuk backup jika initTheme.js bermasalah
    const toggle = document.getElementById('toggle-dark');
    const body = document.documentElement;

    if (localStorage.getItem('theme') === 'dark') {
        toggle.checked = true;
        body.setAttribute('data-bs-theme', 'dark');
    }

    toggle.addEventListener('change', function() {
        if (this.checked) {
            body.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            body.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    });
</script>
</body>
</html>