			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo ADMIN_JS ?>jquery-3.3.1.min.js" type="text/javascript" ></script>
		<?php

		if(isset($_GET['editor'])) { ?>
			<script src='<?php echo ADMIN_JS ?>editor.js' type='text/javascript' ></script>
		<?php
		}
		if(isset($_GET['msg'])) { ?>
			<script type='text/javascript'>
				var msgType = '<?php echo ucwords($postType) ?>'
				var msgText = '<?php echo $_GET['msg'] ?>'
				var msgStatus = '<?php echo $_GET['error'] ?>'
			</script>
		<?php	
		}
		?>
		<script src="<?php echo ADMIN_PLUGINS ?>daterangepicker/moment.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_PLUGINS ?>sweetalert/sweetalert2.all.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_PLUGINS ?>daterangepicker/daterangepicker.js" type="text/javascript" ></script>
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