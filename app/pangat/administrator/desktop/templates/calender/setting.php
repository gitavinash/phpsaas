<h2>Calender setting</h2>

<?php

/** Check month range setting is already defined or not */

if (!defined('CALENDER_MONTH_VISIBLE')) {

	define('CALENDER_MONTH_VISIBLE', 2);
}


?>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" onSubmit="submitCategory()">
				<h3>Manage calendar</h3>
				<div class="form-group">
					<label>Enter how many months visible in the calender</label>
					<input type="number" onwheel="this.blur()" name="month" id="month" class="form-control inside-shadow" placeholder="Ex: 1, 3, 5" value="<?php echo CALENDER_MONTH_VISIBLE ?>" autofocus>
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="calender">
					<input type="hidden" name="action" value="months">
					<input type="submit" id="updateCalenderMonths" class="btn btn-green" value="Update calender">
				</div>
			</form>
		</div>
	</div>
</div>