<?php

	$user_id=mysqli_real_escape_string($connection, $_GET['id']);

?>
<h2>Customer profile</h2>

<?php

    if($employeeProfile=mysqli_query($connection, "SELECT profile_display_name, user_email, user_mobile,  FROM {$table_prefix}company, {$table_prefix}department, {$table_prefix}profile, {$table_prefix}employee, {$table_prefix}user, {$table_prefix}salary_structure WHERE user_id=profile_owner_id AND employee_profile_id=profile_id AND employee_company_id=company_id AND employee_dept_id=department_id AND employee_id={$employee_id} AND salary_structure_employee_id='{$employee_id}' AND salary_structure_status=1")) {

        if(mysqli_num_rows($employeeProfile)!=0) {

            extract(mysqli_fetch_assoc($employeeProfile)); ?>

            <table style='margin-bottom: 20px;'>
                <tr>
                    <td>Employee Name</td>
                    <td><?php echo $profile_display_name; ?></td>
                    <td>Employee Email</td>
                    <td><?php echo $user_email ?></td>
                </tr>
                <tr>
                    <td>Employee Compnay</td>
                    <td><?php echo $company_name ?></td>
                    <td>Employee Department</td>
                    <td><?php echo $department_name ?></td>
                </tr>
                <tr>
                    <td>Employee Salary</td>
                    <td><i class='fas fa-rupee-sign'></i><?php echo $salary_structure_gross ?></td>
                    <td>Employee Machine ID</td>
                    <td><?php echo $employee_machine_code ?></td>
                </tr>
                <tr>
                    <td>Employee Casual Leave Balance</td>
                    <td><?php echo $leave_cl_balance ?></td>
                    <td>Employee Medical Leave Balance</td>
                    <td><?php echo $leave_ml_balance ?></td>
                </tr>
            </table>

            <?php
        }
        
    }

?>