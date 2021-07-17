<?php

    if($checkCalenderStartDate=mysqli_query($connection, "SELECT site_configuration_value FROM ".TABLE_PREFIX."site_configuration WHERE site_configuration_key='calender_start' AND site_configuration_site_id=".SITE_ID)) {

        if(mysqli_num_rows($checkCalenderStartDate)!=0) {

            extract(mysqli_fetch_assoc($checkCalenderStartDate));

            $month=date('m', strtotime($site_configuration_value));
            $year=date('Y', strtotime($site_configuration_value));
                
        } else {

            $month='';
            $year='';

        }

    }

?>

<h2>Manage calender starting month </h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <select class="form-control" id="blockMonth" name="month" style="width: 200px" required>
                <option value="">Select month</option>
                <option value="01" <?php echo (($month=='01') ? 'selected' : '' ) ?>>January</option>
                <option value="02" <?php echo (($month=='02') ? 'selected' : '' ) ?>>Febuary</option>
                <option value="03" <?php echo (($month=='03') ? 'selected' : '' ) ?>>March</option>
                <option value="04" <?php echo (($month=='04') ? 'selected' : '' ) ?>>April</option>
                <option value="05" <?php echo (($month=='05') ? 'selected' : '' ) ?>>May</option>
                <option value="06" <?php echo (($month=='06') ? 'selected' : '' ) ?>>June</option>
                <option value="07" <?php echo (($month=='07') ? 'selected' : '' ) ?>>July</option>
                <option value="08" <?php echo (($month=='08') ? 'selected' : '' ) ?>>August</option>
                <option value="09" <?php echo (($month=='09') ? 'selected' : '' ) ?>>September</option>
                <option value="10" <?php echo (($month=='10') ? 'selected' : '' ) ?>>October</option>
                <option value="11" <?php echo (($month=='11') ? 'selected' : '' ) ?>>November</option>
                <option value="12" <?php echo (($month=='12') ? 'selected' : '' ) ?>>December</option>
            </select>
            <select class="form-control" id="blockYear" name="year" style="width: 200px" required>
                <option value="">Select year</option>
                <?php
                
                    for($i=date('Y'); $i<=date('Y', strtotime('+5 years')); $i++) {

                        echo "<option value='{$i}' ".(($year==$i) ? 'selected' : '' )." >{$i}</option>";

                    }

                ?>
            </select>
            <input type="submit" id="startCalenderDate" class="btn" value="Update Calender">
        </form>
    </div>
    <div class="right-box">

    </div>

</div>