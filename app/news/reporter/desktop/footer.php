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
		<script src="<?php echo ADMIN_PLUGINS ?>sweetalert/sweetalert2.all.min.js" type="text/javascript" ></script>
		<script src="<?php echo ADMIN_JS ?>script.min.js"></script>

	</body>

</html>