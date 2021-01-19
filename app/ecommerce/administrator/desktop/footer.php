			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript" ></script>
		<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript" ></script>
		<script src="<?php echo DEFAULT_ADMIN_JS ?>script.min.js"></script>

		<?php

			if(isset($_GET['editor'])) { ?>
				<script type='text/javascript'>
					$(window).bind('beforeunload', function(){
						return "Leaving this page will reset the wizard";
					});
				</script>
			<?php
			}
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='list' ||  $_GET['type']=='category') { ?>

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