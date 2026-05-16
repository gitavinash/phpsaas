			</div>

			</main>

			<footer>
			    <div class="footer-inner">
			        <div>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</div>
			        <div>Powered by <a href="https://blumox.com" target="_blank">Blumox Technologies</a></div>
			    </div>
			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
			<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
			<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
			<script src="<?php echo JS_LIBRARY ?>admin-menu-builder.min.js"></script>
			<script>
(function() {
    var body = document.body;
    var aside = document.querySelector('aside');
    var toggleBtn = document.getElementById('nav-toggle');
    var overlay = document.getElementById('nav-overlay');
    var mediaQuery = window.matchMedia('(max-width: 1024px)');

    function closeMobileNav() {
        if (!body || !aside || !overlay) {
            return;
        }

        body.classList.remove('mobile-nav-open');
        aside.classList.remove('open');
        overlay.classList.remove('show');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (!mediaQuery.matches || !body || !aside || !overlay) {
                return;
            }

            var isOpen = aside.classList.contains('open');
            if (isOpen) {
                closeMobileNav();
            } else {
                body.classList.add('mobile-nav-open');
                aside.classList.add('open');
                overlay.classList.add('show');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileNav);
    }

    window.addEventListener('resize', function() {
        if (!mediaQuery.matches) {
            closeMobileNav();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMobileNav();
        }
    });

    document.addEventListener('click', function(event) {
        if (!mediaQuery.matches || !body || !aside) {
            return;
        }

        var insideSidebar = event.target.closest('aside');
        var clickedToggle = event.target.closest('#nav-toggle');

        if (!insideSidebar && !clickedToggle && aside.classList.contains('open')) {
            closeMobileNav();
        }
    });
})();
			</script>

			<?php

            if (isset($_GET['responseMsg'])) { ?>

			<script type='text/javascript'>
alert("<?php echo $_GET['responseMsg'] ?>");
			</script>

			<?php
            }

            if (isset($_GET['type'])) {

                if ($_GET['type'] == 'edit' || $_GET['type'] == 'add') { ?>
			<script type='text/javascript'>
$(window).bind('beforeunload', function() {
    return "Leaving this page will reset the wizard";
});
			</script>
			<?php
                }
            }


            if (isset($_GET['type'])) {

                if (in_array($_GET['type'], ['list', 'sessions', 'subscriber', 'all-subscriber'])) { ?>

			<script type='text/javascript'>
$(document).ready(function() {
    var $table = $('#table');
    var defaultOrderColumn = parseInt($table.attr('data-default-order-column'), 10);
    var defaultOrderDir = $table.attr('data-default-order-dir') || 'desc';
    var tableOptions = {
        "bDestroy": true,
        "createdRow": function(row, data, dataIndex) {
            if (data[6] == "Confirm") {
                $(row).css({
                    "background-color": "#FFFFFF",
                    "color": "#333"
                })
            } else if (data[6] == "Cancel") {
                $(row).css({
                    "background-color": "#C70039",
                    "color": "#fff"
                })
            } else if (data[2] == "No") {
                $(row).css({
                    "background-color": "#C70039",
                    "color": "#fff"
                })
            } else if (data[6] == "Delivered") {
                $(row).css({
                    "background-color": "#FFFFFF",
                    "color": "#333"
                })
            } else {
                $(row).css({
                    "background-color": "#E6E6FA",
                    "color": "#333"
                })
            }
        }
    };

    if (!isNaN(defaultOrderColumn)) {
        tableOptions.order = [
            [defaultOrderColumn, defaultOrderDir]
        ];
    } else {
        tableOptions.order = [
            [3, 'desc']
        ];
    }

    $table.DataTable(tableOptions);
});
			</script>
			<?php

                }
            }

            ?>

			</body>

			</html>