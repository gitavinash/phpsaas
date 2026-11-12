<?php
$egAdminJsFile = ROOT . ADMIN_JS . 'ecom-admin.js';
$egAdminJsVer = file_exists($egAdminJsFile) ? filemtime($egAdminJsFile) : time();
?>
        </div>
    </main>

    <footer>
        <div class="footer-inner">
            <div>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?>. All rights reserved.</div>
            <div>Powered by <a href="https://blumox.com" target="_blank" rel="noopener noreferrer">Blumox Technologies</a></div>
        </div>
    </footer>

    <script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
<?php if (defined('ADMIN_BASE_URL') && (ADMIN_BASE_URL === '/appearance/' || ADMIN_BASE_URL === '/setting/') && isset($_GET['type']) && $_GET['type'] === 'menu'): ?>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="<?php echo JS_LIBRARY ?>admin_menu_builder.min.js"></script>
<?php endif; ?>
    <script src="<?php echo ADMIN_JS ?>admin.min.js"></script>
    <script src="<?php echo ADMIN_JS ?>ecom-admin.js?v=<?php echo $egAdminJsVer; ?>"></script>
</body>

</html>
