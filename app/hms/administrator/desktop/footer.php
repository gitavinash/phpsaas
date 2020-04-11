			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src='<?php echo JS_LIBRARY ?>jquery.min.js' type='text/javascript' ></script>
		<script src='<?php echo JS_LIBRARY ?>editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert2.all.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_JS ?>script.min.js"></script>
		

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