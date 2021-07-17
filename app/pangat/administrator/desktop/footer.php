			</div>

			</main>

			<footer>

			</footer>

			<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
			<script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
			<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.js' type='text/javascript'></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/jszip.min.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/buttons.min.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/pdfmake.min.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/vfs_fonts.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/buttons.html5.min.js"></script>
			<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script> -->
			<script src="<?php echo ADMIN_JS ?>calender.min.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js"></script>
			<script src="<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js"></script>
			<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
			<script src="<?php echo ADMIN_JS ?>admin.min.js"></script>

			<?php
		
			if(isset($_GET['type'])) {

				if($_GET['type']=='edit' || $_GET['type']=='add') { ?>
			<script type='text/javascript'>
$(window).bind('beforeunload', function() {
    return "Leaving this page will reset the wizard";
});
			</script>
			<?php
				}

			}

			if($_GET['type']=='list' || $_GET['type']=='single' || $_GET['type']=='multiple') { ?>

			<script type='text/javascript'>
$(document).ready(function() {
    $('#table').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excel',
            text: 'Download Excel file'
        }]
    });
});
			</script>

			<script>
if ($('#dateBlockRange').lenght != 0) {
    $('#dateBlockRange').daterangepicker({
        minDate: new Date(),
        // disabledDates: ["08/24/2021", "08/28/2021", "09/02/2021", "09/23/2021"],
        "maxSpan": {
            "days": 10
        },
        "locale": {
            // "format": "DD-l-YYYY",
            "format": "ll",
            "separator": " - ",
            "applyLabel": "Apply",
            "cancelLabel": "Cancel",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "weekLabel": "W",
            "daysOfWeek": [
                "Su",
                "Mo",
                "Tu",
                "We",
                "Th",
                "Fr",
                "Sa"
            ],
            "monthNames": [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ],
            "firstDay": 1
        },
    });
}
			</script>



			<?php
			}

		?>

			</body>

			</html>