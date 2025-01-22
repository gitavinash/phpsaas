				</div>

				<!-- <footer>
					<div class="credit">
						<p><a href="<?php echo CREDIT_URL ?>" title="Developed by <?php echo CREDIT ?>" target="_blank">Powered by
								<a href='https://aleya.in?utm_source=<?php echo strtolower(str_replace(' ', '_', SITE_NAME)) ?>'
									target="_blank"><?php echo ucwords(str_replace('_', ' ', SITE_APP_NAME)) ?><sup
										class="trademark">TM</sup></a> | Made
								with <i class="fi-rr-heart"></i> in
								India by <a href="https://blumox.com?utm_source=aleya&utm_medium=dashboard"><strong>Blumox
										Technologies</strong></a></p>
					</div>
				</footer> -->
				</main>

				<?php require_once NOTIFICATION . 'index.php' ?>
				<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
				<script src="<?php echo JS_LIBRARY ?>tinymce/tinymce.min.js"></script>
				<script>
					// Select all elements with the class 'editor'
					const editors = document.querySelectorAll('.editor');

					// Initialize an array to store Quill instances
					const quillInstances = [];

					// Loop through each editor element
					editors.forEach((editor, index) => {
						// Create a new Quill instance
						const quill = new Quill(editor, {
							theme: 'snow'
						});

						// Set the initial content
						const hiddenTextarea = document.getElementById(`editor-${index}`);
						quill.root.innerHTML = hiddenTextarea.value;

						// Update the hidden input value whenever the content changes
						quill.on('text-change', function() {
							hiddenTextarea.value = quill.root.innerHTML;
						});

						// Store the Quill instance in the array
						quillInstances.push(quill);
					});
					$(document).ready(function() {
						$(".showInventoryTableList").DataTable({
							pageLength: 50,
							fixedHeader: true, // Ensure the header is fixed
							responsive: false, // Disable responsiveness as it may conflict with FixedColumns
							searching: true,
							lengthChange: false,
							autoWidth: false,
							ordering: false,
							scrollX: true, // Enable horizontal scrolling
							scrollCollapse: true, // Allow table to collapse to fit content
							columnDefs: [{
									type: "date",
									targets: 4, // Assuming the date column is the fifth column (index 4)
								},
								{
									width: "20%",
									targets: 1, // Set width for the second column
								},
								{
									width: "150px",
									targets: -1, // Set minimum width for the last column
								},
								{
									targets: "_all", // Apply to all columns
									createdCell: function(td, cellData, rowData, row, col) {
										$(td).css({
											"max-width": "150px", // Set maximum width
											"white-space": "normal", // Allow text wrapping
											"word-wrap": "break-word", // Ensure words break if necessary
										});
									},
								},
							],
							createdRow: function(row, data, dataIndex) {
								// Get the status from the row data
								var rowDataStatus = data[data.length - 2].toLowerCase().replace(" ", "_");

								// Map status to background color
								var colors = {
									draft: "#f9e29f",
									initiate: "#f5f5f5",
									active: "#b4e2b4",
									approved: "#b4e2b4",
									sold_out: "#b87085",
									success: "#A9D5D1",
									duplicate: "#C8C8F0",
									follow_up: "#E6AF60",
									payment_initiate: "#B3E6F0",
									payment_refund: "#F6B0DB",
									failed: "#F8B3B3",
									soft_delete: "#F5B0B0",
									delete: "#F8A6A6",
								};

								var color = colors[rowDataStatus] || "#D1D2D4"; // Default to grey

								// Apply background color to row
								$(row).css("background-color", color);
								$(row).find("td").css("background-color", color + " !important");
							},
							fixedColumns: {
								leftColumns: 2, // Freeze the first two columns
								rightColumns: 1, // Freeze the last column
							},
							order: [
								[4, "desc"],
							], // Order by the fifth column (date)
						});
					});
				</script>
				<?php

				if ($site_access_type == 'agent') { ?>
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