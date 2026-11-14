				</div>

				<?php

				if (SITE_VERSION != 'mobile') { ?>
					<footer>
						<div class="credit">
							<p><a href="<?php echo (defined('CREDIT_URL') && CREDIT_URL != '') ? CREDIT_URL : 'http://www.blumox.com/' ?>" title="Developed by <?php echo defined('CREDIT') ? CREDIT : 'Blumox Technologies' ?>" target="_blank">Powered by</a> <a href='https://aleya.in/?utm_source=<?php echo strtolower(str_replace(' ', '_', defined('SITE_NAME') ? SITE_NAME : 'gracious_world')) ?>' target="_blank"><?php echo ucwords(str_replace('_', ' ', defined('SITE_APP_NAME') ? SITE_APP_NAME : 'Aleya')) ?><sup class="trademark">TM</sup></a> | Made with <i class="fi-rr-heart"></i> in India by <a href="https://blumox.com/?utm_source=aleya&utm_medium=dashboard" target="_blank"><strong>Blumox Technologies</strong></a></p>
						</div>
					</footer>
				<?php
				}

				?>
				</main>

				<?php require_once NOTIFICATION . 'index.php' ?>
				<script src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js' type='text/javascript'></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
				<script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>
				<script src="https://cdn.datatables.net/fixedcolumns/4.0.0/js/dataTables.fixedColumns.min.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/responsive.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>chartjs/chart.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
				<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>drag_and_drop/drag_and_drop.min.js"></script>
				<script src="<?php echo JS_LIBRARY ?>tinymce/tinymce.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.js"></script>
				<script>
					const siteAdminType = '<?php echo $site_access_type ?>';
				</script>
				<?php
				if (SITE_VERSION == 'mobile') { ?>

					<!-- Mobile JS -->
					<script>
						(function() {
							var openBtn = document.getElementById('moreOpen');
							var panel = document.getElementById('morePanel');
							var closeBtn = document.getElementById('moreClose');

							if (!openBtn || !panel || !closeBtn) return;

							function openMore() {
								panel.setAttribute('aria-hidden', 'false');
								document.body.classList.add('more-open');
								try {
									closeBtn.focus();
								} catch (e) {}
							}

							function closeMore() {
								panel.setAttribute('aria-hidden', 'true');
								document.body.classList.remove('more-open');
								try {
									openBtn.focus();
								} catch (e) {}
							}

							// Open/Close controls
							openBtn.addEventListener('click', openMore);
							closeBtn.addEventListener('click', closeMore);

							// Close on Esc
							document.addEventListener('keydown', function(e) {
								if (e.key === 'Escape') closeMore();
							});

							// Close panel after selecting a menu item (nice on mobile)
							panel.querySelectorAll('.more-item, .logout-btn').forEach(function(el) {
								el.addEventListener('click', closeMore);
							});

							// Optional: close when clicking empty space beside content (only if target is the outer panel)
							panel.addEventListener('click', function(e) {
								if (e.target === panel) closeMore();
							});
						})();
					</script>


				<?php
				}
				?>

				<?php

				if ($site_access_type == 'agent') { ?>
					<script>
						const tempId = '<?php echo base64_encode(USER_ID) ?>';
					</script>
				<?php
				}

				?>
				<script src="<?php echo ADMIN_JS ?>app.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>table.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>packages.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>package-domestic-add.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>package-edit.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>gallery.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>package-price-list.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>package-hotel-list.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>package-transport.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>transporters.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>transporter-add.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>transporter-vehicles.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>users.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>properties.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>property-add.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>property-update.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>amenities.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>room-types.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>user-form.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>leads.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>leads-add.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>lead-view.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>transporter-quote.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>report-employees.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>settings.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>company-list.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>company-add.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>company-update.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>v3-editor.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>policy-list.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>policy-form.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>integration.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>whatsapp.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>accounts.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>marketing-dashboard.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>web_forms.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>whatsapp_templates.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>workflows.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>control_panel.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>configurations.js?v=<?php echo time(); ?>"></script>
				<script src="<?php echo ADMIN_JS ?>operations.js?v=<?php echo time(); ?>"></script>
				<?php
				$appJSPath = ADMIN_PATH . 'assets/js/' . SITE_APP . '.js';
				$appJSScript = ADMIN_JS . SITE_APP . '.js';
				if (file_exists($appJSPath)) {
					echo "<script src='{$appJSScript}?v=" . time() . "'></script>";
				}
				?>
				<?php require_once PWA . 'footer.php'; ?>
				</body>

				</html>