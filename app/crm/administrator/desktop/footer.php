			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript" ></script>
		<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>datatable/datatables.min.js" type="text/javascript" ></script>
		<script src="<?php echo DEFAULT_ADMIN_JS ?>script.min.js"></script>
		<script src="<?php echo APP_JS_LIBRARY ?>app.min.js"></script>

		<?php
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='edit' || $_GET['type']=='add') { ?>
					<script type='text/javascript'>
						$(window).bind('beforeunload', function(){
							return "Leaving this page will reset the wizard";
						});
					</script>
				<?php
				}

				if($_GET['type']=='list') { ?>

					<script type='text/javascript'>
						$(document).ready( function () {
							$('#table').DataTable({
								"createdRow": function(row, data, dataIndex) {
								if (data[6] == "Confirm") {
									$(row).css({
										"background-color": "#9FFF33",
										"color": "#333"
									})
								} else if (data[6] == "Cancel") {
									$(row).css({
										"background-color": "#C70039",
										"color": "#333"
									})
								} else {
									$(row).css({
										"background-color": "#FFC300",
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