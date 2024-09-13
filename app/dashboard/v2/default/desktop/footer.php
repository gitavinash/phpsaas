				</div>

				<footer>
				    <div class="credit">
				        <p><a href="<?php echo CREDIT_URL ?>" title="Developed by <?php echo CREDIT ?>" target="_blank">Powered by
				                <a href='https://aleya.in?utm_source=<?php echo strtolower(str_replace(' ', '_', SITE_NAME)) ?>'
				                    target="_blank"><?php echo ucwords(str_replace('_', ' ', SITE_APP_NAME)) ?><sup
				                        class="trademark">TM</sup></a> | Made
				                with <i class="fi-rr-heart"></i> in
				                India by <a href="https://blumox.com?utm_source=aleya&utm_medium=dashboard"><strong>Blumox
				                        Technologies</strong></a></p>
				    </div>
				</footer>
				</main>

				<?php require_once NOTIFICATION . 'index.php'?>
				<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js' type='text/javascript'></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
				<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/responsive.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>chartjs/chart.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
				<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
				<script>
				const quill = new Quill('.editor', {
					theme: 'snow'
				});
				</script>
				<?php

if ($site_access_type == 'agent') {?>
				<script>
const tempId = '<?php echo base64_encode(USER_ID) ?>';
				</script>
				<?php
}

?>
				<script src="<?php echo ADMIN_JS ?>app.min.js"></script>
				<script src="<?php echo ADMIN_JS . SITE_APP ?>.min.js"></script>
				</body>

				</html>