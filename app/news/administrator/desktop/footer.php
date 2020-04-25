			</div>

		</main>

		<footer>
			
		</footer>
		
        <?php require_once NOTIFICATION.'index.php'; ?>
		<script src='<?php echo JS_LIBRARY ?>editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/moment.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert2.all.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_JS ?>script.min.js"></script>
		
		

		<?php
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='edit' || $_GET['type']=='add') { ?>

					<script type='text/javascript'>
						$(window).bind('beforeunload', function(){
							return 'Are you sure you want to leave?';
						});
					</script>

					<script type="text/javascript">
						$('.datetimepicker').daterangepicker({
							"singleDatePicker": true,
							"timePicker": true,
							"applyButtonClasses": "btn-green",
							"cancelButtonClasses": "btn-red",
							"minDate" : "<?php echo date('m/d/Y'); ?>",
							"startDate" : "<?php echo date('m/d/Y H:i A'); ?>",
							locale: {
            					format: 'MM/DD/YYYY hh:mm A'
							},
						});
					</script>

				<?php
				}

			}

		?>

	</body>

</html>