<script src="<?= base_url('assets/'.$THEME.'/js/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/js/popper.min.js'); ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/js/bootstrap.min.js'); ?>"></script>

<script src="<?= base_url('assets/'.$THEME.'/js/Chart.min.js'); ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/js/Chart.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/js/semantic.min.js'); ?>"></script>

<script src="<?= base_url('assets/'.$THEME.'/js/perfect-scrollbar.min.js'); ?>"></script>
<script src="<?= base_url('assets/'.$THEME.'/js/custom.js'); ?>"></script>

<script>
    $(document).ready(function() {
        // Fix Sidebar Toggle
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
        });
        
        // Inisialisasi scrollbar
        if ($('#sidebar').length > 0) {
            new PerfectScrollbar('#sidebar');
        }
    });
</script>