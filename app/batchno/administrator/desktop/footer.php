			</div>

			</main>

			<footer>

			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
			<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
			<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>

			<?php

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