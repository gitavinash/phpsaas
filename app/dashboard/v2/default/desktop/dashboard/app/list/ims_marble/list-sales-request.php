<?php
$tableNamePrefix = TABLE_PREFIX . strtolower(SITE_APP) . '_';
?>
<table class="showTableList">
    <thead>
        <tr>
            <th>Request no.</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Agent Name</th>
            <th>Agent Mobile</th>
            <th>Sales date</th>
            <th></th>
        </tr>
    </thead>
    <?php

    if ($salesList = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=sales_agent_id WHERE sales_status=0 AND sales_site_id='" . SITE_ID . "' ORDER BY sales_created DESC")) {
        echo "<tbody>";
        if (mysqli_num_rows($salesList) > 0) {
            while ($salesData = mysqli_fetch_assoc($salesList)) {
                extract($salesData);
                echo "
                <tr>
                    <td>{$sales_id}</td>
                    <td>{$sales_name}</td>
                    <td>{$sales_mobile}</td>
                    <td>{$profile_display_name}</td>
                    <td>{$profile_mobile}</td>
                    <td>" . date('d M Y h:i:s A', strtotime($sales_created)) . "</td>
                    <td>
                        <a href='/dashboard/sales_request/view/?id=" . base64_encode($sales_id) . "' class='tableActionIconBtn' title='Edit sales'><i class='fi fi-rr-eye bgLightGreen'></i></a>
                        <a href='#' data-type='sales' data-id='{$sales_id}' title='Delete sales' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                    </td>
                </tr>";
            }
        }
        echo "</tbody>";
    } else {
        echo mysqli_error($connection);
    }

    ?>
</table>