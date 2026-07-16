</div><!-- /container -->

<footer class="py-3 mt-4 border-top bg-white text-center text-muted small">
    <div class="container">
        &copy; <?= date('Y') ?> <strong>PS-RENTAL</strong> &mdash; Sistem Manajemen Penyewaan PlayStation
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Active navbar link highlight -->
<script>
(function () {
    const base = <?= json_encode(BASE_URL) ?>;
    const path = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        const href = link.getAttribute('href');
        if (!href) return;
        // Exact match for dashboard, prefix match for others
        if (path === href || (href !== base + '/index.php' && path.startsWith(href.replace(/\/[^/]+\.php$/, '')))) {
            link.classList.add('active');
        }
    });
})();
</script>
</body>
</html>
