			</div>

		</main>

		<footer>
			
		</footer>
		
        <?php require_once NOTIFICATION.'index.php'; ?>
		<script src='<?php echo JS_LIBRARY ?>editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/moment.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert2.all.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_GLOBAL_JS ?>script.min.js"></script>
		
		

		<?php
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='edit' || $_GET['type']=='add') { ?>

					<script type='text/javascript'>
						$(window).bind('beforeunload', function(){
							return 'Are you sure you want to leave?';
						});
					</script>

				<?php
				}

			}

		?>

	</body>

</html>