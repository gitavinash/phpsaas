        </main>
        <footer>
            
        </footer>
        <?php require_once NOTIFICATION.'index.php'; ?>
        <scipt type="text/javascript">
            $(document).ready(function(e) {
                var date = <?php echo ((isset($_GET['date'])) ? $_GET['date'] : date('01-M')); ?>
            });
        </scipt>
        <script src="<?php echo APP_JS_LIBRARY ?>app.min.js" type="text/javascript" ></script>
    </body>
</html>