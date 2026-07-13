			</div>

			</main>

			<footer>
			    <div class="footer-inner">
			        <div>© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</div>
			        <div>Powered by <a href="https://blumox.com" target="_blank">Blumox Technologies</a></div>
			    </div>
			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
			<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
			<script src="<?php echo JS_LIBRARY ?>admin_menu_builder.min.js"></script>
			<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>
			<script>
			    (function() {
			        var body = document.body;
			        var toggleBtn = document.getElementById('nav-toggle');
			        var overlay = document.getElementById('nav-overlay');
			        var mediaQuery = window.matchMedia('(max-width: 1024px)');

			        function closeMobileNav() {
			            body.classList.remove('sidebar-open');
			        }

			        if (toggleBtn) {
			            toggleBtn.addEventListener('click', function() {
			                if (!mediaQuery.matches) {
			                    return;
			                }
			                body.classList.toggle('sidebar-open');
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
			            if (!mediaQuery.matches) {
			                return;
			            }
			            var insideSidebar = event.target.closest('aside');
			            var clickedToggle = event.target.closest('#nav-toggle');
			            if (!insideSidebar && !clickedToggle && body.classList.contains('sidebar-open')) {
			                closeMobileNav();
			            }
			        });
			    })();
			</script>

			<?php

            if (isset($_GET['type'])) {

                if ($_GET['type'] == 'list' ||  $_GET['type'] == 'category' || $_GET['type'] == 'tags' || $_GET['type'] == 'category') { ?>

			        <script>
			            $(document).ready(function() {
			                var $table = $('#table');
			                var defaultOrderColumn = parseInt($table.attr('data-default-order-column'), 10);
			                var defaultOrderDir = $table.attr('data-default-order-dir') || 'asc';
			                var tableOptions = {
			                    bDestroy: true,
			                    autoWidth: false,
			                    responsive: true,
			                    pageLength: 10,
			                    lengthMenu: [
			                        [10, 25, 50, 100],
			                        [10, 25, 50, 100]
			                    ],
			                    language: {
			                        lengthMenu: 'Rows _MENU_',
			                        search: '',
			                        searchPlaceholder: 'Search records...'
			                    }
			                };

			                if (!isNaN(defaultOrderColumn)) {
			                    tableOptions.order = [
			                        [defaultOrderColumn, defaultOrderDir]
			                    ];
			                }

			                $table.DataTable(tableOptions);
			            });
			        </script>

			    <?php
                } else { ?>

			        <script>
			            $(document).ready(function() {
			                function hideSelectedOption(data) {
			                    if (!data.id) {
			                        return data.text;
			                    }
			                    return data.selected ? null : data.text;
			                }

			                $('.selectSimilarProduct').select2({
			                    templateResult: hideSelectedOption
			                });
			            })
			        </script>
			<?php
                }
            }

            ?>

			</body>

			</html>