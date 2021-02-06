<h2>Calender setting</h2>

<?php

    /** Check month range setting is already defined or not */

    if($checkSetting=mysqli_query($connection, "SELECT site_configuration_value as calender_month_visible FROm ".TABLE_PREFIX."site_configuration WHERE site_configuration_site_id=".APP_SITE_ID." AND site_configuration_name='calender_month_visible'")) {

        if(mysqli_num_rows($checkSetting)!=0) {

            extract(mysqli_fetch_assoc($checkSetting));

        } else {

            $calender_month_visible=2;

        }

    }


?>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" onSubmit="submitCategory()" >
				<h3>Manage calendar</h3>
				<div class="form-group">
					<label>Enter how many months visible in the calender</label>
					<input type="number" name="month" id="month" class="form-control inside-shadow" placeholder="Ex: 1, 3, 5" value="<?php echo $calender_month_visible ?>" autofocus >
				</div>
				<div class="form-group">
                    <input type="hidden" name="type" value="calender" >
                    <input type="hidden" name="action" value="months" >
					<input type="submit" id="updateCalenderMonths" class="btn btn-green" value="Update calender" >
				</div>
			</form>
		</div>
    </div>
</div>