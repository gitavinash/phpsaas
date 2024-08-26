<?php

if (!isset($id)) { ?>
<script>
alert('Sales request id is missing');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
} else {
    if ($id == '') { ?>
<script>
alert('Sales request id cannot be blank');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
    } else {
        $salesId = base64_decode($id);
    }
}

if ($checkSalesRequest = mysqli_query($connection, "SELECT * FROM {$tableNamePrefix}sales INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=sales_agent_id INNER JOIN " . TABLE_PREFIX . "warehouse ON warehouse_id=sales_warehouse_id INNER JOIN " . TABLE_PREFIX . "address ON warehouse_address_id=address_id INNER JOIN " . TABLE_PREFIX . "city ON address_city_id=city_id INNER JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id WHERE sales_status=0 AND sales_id='{$salesId}' AND sales_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkSalesRequest) == 0) { ?>
<script>
alert('Invalid Sales request id found');
window.location.href = '/dashboard/sales_request/list/';
</script>
<?php
    } else {
        extract(mysqli_fetch_assoc($checkSalesRequest));
    }
} else {
    echo mysqli_error($connection);
}

?>
<h2 class="heading">Sales request details<span>Complete information of sales request</span></h2>
<div class="box-1">

    <div class="widget box-7-5 no-padding">
        <div class="main-box">
            <section>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="laadDetailsBtn" class="btn btn-green"><i class="fi-rr-review"></i>
                            Customer details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $sales_name ?></p>
                        <p><strong>Mobile</strong><?php echo $sales_mobile ?></p>
                        <p><strong>Email</strong><?php echo $sales_email ?></p>
                        <p><strong style="width: 100px; height: 50px">Address</strong><?php echo $sales_address ?>
                        </p>
                    </div>
                </div>
                <div class="widget no-padding box-5">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-id-card-clip-alt"></i>
                            Salesman details</a>
                    </div>
                    <div class="userInfo">
                        <p><strong>Name</strong><?php echo $profile_display_name ?></p>
                        <p><strong>Mobile</strong><?php echo $profile_mobile ?></p>
                        <p><strong>GST Number</strong><?php echo $warehouse_gst ?></p>
                        <p><strong style="width: 100px; height: 50px">Warehouse
                            </strong><?php echo ucwords($warehouse_title) . ', ' . $address_area . ', ' . $city_name . ', ' . $state_name ?>
                        </p>
                    </div>
                </div>
                <div class="widget no-padding box-1">
                    <div class="btn-group leadActions">
                        <a id="leadDetailsBtn" class="btn btn-green"><i class="fi-rr-to-do"></i>
                            Inventory details</a>
                    </div>

                    <div class="full-width">
                        <table class="stockInfo">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="sno-column">S No.</th>
                                    <th rowspan="2">Block Name</th>
                                    <th class="text-center" colspan="100">Slabs Details</th>
                                </tr>
                                <tr>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Gross Qty.</th>
                                    <th>Damage Qty.</th>
                                    <th>Net Qty.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $blockData = json_decode($sales_data, TRUE);
                                $i = 1;
                                $totalBlockSlabs = 0;
                                $totalBlockGrossSize = 0;
                                $totalBlockDamageSize = 0;
                                $totalBlockNetSize = 0;
                                $totalBlockDimensionType = '';

                                foreach ($blockData as $key => $value) {
                                    $keyArray = explode('_', str_replace('salesBlocks_', '', $key));
                                    $productName = $keyArray[1];

                                    // Exploding string and processing data
                                    $objects = explode(",", $value);
                                    $valuesCount = [];
                                    $slabDetails = [];

                                    foreach ($objects as $object) {
                                        list($number, $slabDimension, $grossSize, $damageSize, $netSize, $dimensionsType) = explode("_", $object);
                                        if (isset($valuesCount[$slabDimension])) {
                                            $valuesCount[$slabDimension]['count']++;
                                            $valuesCount[$slabDimension]['grossSize'] += $grossSize;
                                            $valuesCount[$slabDimension]['damageSize'] += $damageSize;
                                            $valuesCount[$slabDimension]['netSize'] += $netSize;
                                        } else {
                                            $valuesCount[$slabDimension] = [
                                                'count' => 1,
                                                'grossSize' => $grossSize,
                                                'damageSize' => $damageSize,
                                                'netSize' => $netSize,
                                                'dimensionType' => $dimensionsType,
                                            ];
                                        }
                                    }

                                    $j = 1;

                                    $sizeOfSlabDimension = sizeof($valuesCount);

                                    $totalSlabs = 0;
                                    $totalSlabsGrossSize = 0;
                                    $totalSlabsDamageSize = 0;
                                    $totalSlabsNetSize = 0;

                                    // Display slab details
                                    foreach ($valuesCount as $slabDimension => $details) {
                                        echo "<tr " . (($j == 1) ? "class='starting'" : '') . ">";
                                        echo "<td class='sno-column'>" . (($j == 1) ? $i : '') . "</td>";
                                        echo "<td>" . (($j == 1) ? $productName : '') . "</td>";
                                        echo "<td>{$slabDimension}</td>";
                                        echo "<td>{$details['count']} " . (($details['count'] == 1) ? 'Pc.' : 'Pcs.') . "</td>";
                                        echo "<td>{$details['grossSize']} {$details['dimensionType']}</td>";
                                        echo "<td>{$details['damageSize']} {$details['dimensionType']}</td>";
                                        echo "<td>{$details['netSize']} {$details['dimensionType']}</td>";
                                        echo "</tr>";

                                        $totalSlabs += $details['count'];
                                        $totalSlabsGrossSize += $details['grossSize'];
                                        $totalSlabsDamageSize += $details['damageSize'];
                                        $totalSlabsNetSize += $details['netSize'];

                                        if ($j == $sizeOfSlabDimension) {
                                            echo "<tr class='ending'>";
                                            echo "<td class='sno-column' colspan=3><strong>Slab total</strong></td>";
                                            echo "<td>{$totalSlabs} " . (($totalSlabs == 1) ? 'Pc.' : 'Pcs.') . "</td>";
                                            echo "<td>{$totalSlabsGrossSize} {$details['dimensionType']}</td>";
                                            echo "<td>{$totalSlabsDamageSize} {$details['dimensionType']}</td>";
                                            echo "<td>{$totalSlabsNetSize} {$details['dimensionType']}</td>";
                                            echo "</tr><tr><td colspan=100></td></tr>";
                                            $totalBlockSlabs += $totalSlabs;
                                            $totalBlockGrossSize += $totalSlabsGrossSize;
                                            $totalBlockDamageSize += $totalSlabsDamageSize;
                                            $totalBlockNetSize += $totalSlabsNetSize;
                                            $totalBlockDimensionType = $details['dimensionType'];
                                        }

                                        $j++;
                                    }
                                    if ($i == sizeof($blockData)) {
                                        echo "<tr><td colspan=100></td></tr><tr>";
                                        echo "<td colspan=3><strong>Total</strong></td>";
                                        echo "<td>{$totalBlockSlabs} " . (($totalBlockSlabs == 1) ? 'Pc.' : 'Pcs.') . "</td>";
                                        echo "<td>{$totalBlockGrossSize} {$totalBlockDimensionType}</td>";
                                        echo "<td>{$totalBlockDamageSize} {$totalBlockDimensionType}</td>";
                                        echo "<td>{$totalBlockNetSize} {$totalBlockDimensionType}</td>";
                                        echo "</tr>";
                                    }
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="widget no-padding box-2-5" id="fixedDiv">
        <div class="main-box">
            <section class="leadQuickLinks">
                <a href="" class="btn btn-green" id="approveSalesRequest"
                    data-agent="<?php echo base64_encode($sales_agent_id) ?>" data-id="<?php echo $id ?>"><i
                        class="fi-rr-check"></i> Approve
                    request</a>
                <a href='/print_sales_request/?id=<?php echo $id ?>' target="_blank" class="btn btn-green"
                    id="approveSalesRequest"><i class="fi-rr-print"></i> Print</a>
                <a href="#" data-type='sales' data-id='<?php echo $sales_id ?>' title='Delete sales'
                    class='btn btn-red deleteButton' data-reload='false' data-redirect='true'
                    data-url='/dashboard/sales_request/list/'><i class='fi fi-rr-trash bgRed'></i> Delete
                    request</a>
            </section>
        </div>
    </div>
</div>