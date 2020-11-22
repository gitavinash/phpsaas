			</div>

		</main>

		<footer>
			
		</footer>
		
		<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript" ></script>
		<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript' ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript" ></script>
		<script src="<?php echo PLUGIN_LIBRARY ?>datatable/datatables.min.js" type="text/javascript" ></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
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