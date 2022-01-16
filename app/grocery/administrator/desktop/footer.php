			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript" ></script>
		<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
		<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>

		<?php
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='list' ||  $_GET['type']=='category' || $_GET['type']=='tags' || $_GET['type']=='category' ) { ?>

					<script>
						
						$(document).ready( function () {
							$('#table').DataTable();
						});
					</script>

				<?php
				}

			}

		?>

	</body>

</html>