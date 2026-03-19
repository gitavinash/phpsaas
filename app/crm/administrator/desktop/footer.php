			</div>

			</main>

			<footer>

			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
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

				if ($_GET['type'] == 'list') { ?>

					<script type='text/javascript'>
						$(document).ready(function() {
							$('#table').DataTable({
								"createdRow": function(row, data, dataIndex) {
									if (data[6] == "Confirm") {
										$(row).css({
											"background-color": "#FFFFFF",
											"color": "#333"
										})
									} else if (data[6] == "Cancel") {
										$(row).css({
											"background-color": "#C70039",
											"color": "#333"
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
								},
							});
						});
					</script>

			<?php
				}
			}

			?>

			</body>

			</html>