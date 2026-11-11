			</div>

			</main>

			<footer>

			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
			<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
			<script src="<?php echo APP_JS_LIBRARY ?>app.min.js"></script>
			<script>
				(function() {
					var body = document.body;
					var toggleBtn = document.getElementById('nav-toggle');
					var overlay = document.getElementById('nav-overlay');
					var mediaQuery = window.matchMedia('(max-width: 1024px)');

					function closeMobileNav() {
						body.classList.remove('sidebar-open');
					}

					if (toggleBtn && !toggleBtn.dataset.navToggleBound) {
						toggleBtn.dataset.navToggleBound = '1';
						toggleBtn.addEventListener('click', function() {
							if (!mediaQuery.matches) {
								return;
							}
							body.classList.toggle('sidebar-open');
						});

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
					}
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

				if (($_GET['type'] == 'list' || $_GET['type'] == 'sessions') && (!defined('ADMIN_BASE_URL') || ADMIN_BASE_URL !== '/order/')) { ?>

					<script type='text/javascript'>
						$(document).ready(function() {
							if (!$.fn || !$.fn.DataTable) {
								return;
							}
							var $table = $('#table');
							if (!$table.length || $.fn.DataTable.isDataTable($table)) {
								return;
							}

							var $tbody = $table.find('tbody');
							var $rows = $tbody.children('tr');
							var emptyMsg = $.trim($table.attr('data-empty-message') || '');
							if ($rows.length === 1) {
								var $cells = $rows.eq(0).children('td, th');
								if ($cells.length === 1 && $cells.eq(0).is('[colspan]')) {
									if (!emptyMsg) {
										emptyMsg = $.trim($cells.eq(0).text());
									}
									$rows.eq(0).remove();
								}
							}

							var headerCount = $table.find('thead th').length;
							var attrOrderCol = parseInt($table.attr('data-default-order-column'), 10);
							var attrOrderDir = (($table.attr('data-default-order-dir') || 'asc').toLowerCase() === 'desc') ? 'desc' : 'asc';
							var pageLength = parseInt($table.attr('data-page-length'), 10);
							var isModernList = $table.closest('.modern-user-list, .modern-customer-list, .modern-company-list, .modern-vendor-list, .modern-user-sessions').length > 0;

							var tableOptions = {
								autoWidth: false
							};
							if (!isNaN(pageLength) && pageLength > 0) {
								tableOptions.pageLength = pageLength;
							}
							if (emptyMsg) {
								tableOptions.language = {
									emptyTable: emptyMsg
								};
							}

							if (!isNaN(attrOrderCol) && attrOrderCol >= 0 && attrOrderCol < headerCount) {
								tableOptions.order = [
									[attrOrderCol, attrOrderDir]
								];
							} else if (isModernList) {
								tableOptions.order = [];
							} else if (headerCount > 4) {
								tableOptions.order = [
									[4, 'desc']
								];
							}

							if (!isModernList && headerCount >= 7) {
								tableOptions.createdRow = function(row, data) {
									if (data[6] == "Confirm") {
										$(row).css({
											"background-color": "#FFFFFF",
											"color": "#333"
										});
									} else if (data[6] == "Cancel") {
										$(row).css({
											"background-color": "#C70039",
											"color": "#fff"
										});
									} else if (data[6] == "Delivered") {
										$(row).css({
											"background-color": "#FFFFFF",
											"color": "#333"
										});
									} else {
										$(row).css({
											"background-color": "#E6E6FA",
											"color": "#333"
										});
									}
								};
							}

							$table.DataTable(tableOptions);
						});
					</script>

			<?php
				}
			}

			if (defined('ADMIN_BASE_URL') && ADMIN_BASE_URL === '/order/' && (!isset($_GET['type']) || $_GET['type'] === 'list')) { ?>
				<script src="/lib/resource/ui/js/app/crm/desktop/orders-list.js?v=<?php echo @filemtime(ROOT . '/lib/resource/ui/js/app/crm/desktop/orders-list.js') ?: 1; ?>"></script>
			<?php }

			?>

			</body>

			</html>