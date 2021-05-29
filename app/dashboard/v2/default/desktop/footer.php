				</div>

				<footer>
				    <div class="credit">
				        <p><a href="<?php echo CREDIT_URL ?>" title="Developed by <?php echo CREDIT ?>" target="_blank">Made with <i
				                    class="fi-rr-heart"></i> in India.</a></p>
				    </div>
				</footer>
				</main>

				<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js' type='text/javascript'></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
				<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>

				<?php

			if(isset($_GET['editor'])) { ?>
				<script type='text/javascript'>
$(window).bind('beforeunload', function() {
    return "Leaving this page will reset the wizard";
});
				</script>
				<?php
			}
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='list' ||  $_GET['type']=='category') { ?>

				<script>
$(document).ready(function() {
    $('#table').DataTable();
});
				</script>

				<?php
				} ?>

				<?php

			}

		?>

				</body>

				</html>