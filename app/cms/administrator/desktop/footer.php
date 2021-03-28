			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript" ></script>
		<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript" ></script>
		<script src="<?php echo DEFAULT_ADMIN_JS ?>script.min.js"></script>
		<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>

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

			}

		?>

	</body>

</html>