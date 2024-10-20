<script>
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('packageInfo_')) {
            localStorage.removeItem(key);
        }
    }
</script>
<h2 class="heading"><?php echo ucwords($urlStructure[1]) ?> List<span>All hostings list will be displayed here.</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/packages/domestic/price/?id=<?php echo $id ?>" data-title="Add new price"><i class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <div class="list-box">
            <table class="showTableList largeThead">
                <thead>
                    <tr>
                        <th rowspan=2>Duration</th>
                        <th colspan=4>Adult ( Price )</th>
                        <th colspan=4>Child ( Price )</th>
                        <th rowspan=2>Type</th>
                        <th rowspan=2>Status</th>
                        <th rowspan=2></th>
                    </tr>
                    <tr>
                        <th>Net</th>
                        <th>MRP</th>
                        <th>Selling</th>
                        <th>Agent</th>
                        <th>Net</th>
                        <th>MRP</th>
                        <th>Selling</th>
                        <th>Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $packageId = base64_decode($id);

                    if ($packageData = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package_price WHERE tour_package_price_status!=3 AND tour_package_price_parent_id='{$packageId}' AND tour_package_price_site_id=" . SITE_ID . " ORDER BY tour_package_price_created_at DESC")) {
                        if (mysqli_num_rows($packageData) != 0) {
                            $i = 1;
                            while ($packageDataInfo = mysqli_fetch_assoc($packageData)) {

                                $packageDataInfoJSON = json_encode($packageDataInfo, TRUE);
                                echo "
                                <script>
                                localStorage.setItem('packageInfo_{$packageDataInfo['tour_package_price_id']}', JSON.stringify({$packageDataInfoJSON}));
                                </script>
                                ";
                                extract($packageDataInfo);
                                switch ($tour_package_price_status) {
                                    case 1:
                                        $packageStatus = 'Active';
                                        break;
                                    case 2:
                                        $packageStatus = 'Draft';
                                        break;
                                    case 3:
                                        $packageStatus = 'Delete';
                                        break;
                                    default:
                                        $packageStatus = 'Pending';
                                        break;
                                }
                                echo "
                                    <tr>
                                        <td>" . date('d-M', strtotime($tour_package_price_date_start)) . " to " . date('d-M', strtotime($tour_package_price_date_end)) . "</td>
                                        <td>₹ {$tour_package_price_net_adult}</td>
                                        <td>₹ {$tour_package_price_mrp_adult}</td>
                                        <td>₹ {$tour_package_price_selling_adult}</td>
                                        <td>₹ {$tour_package_price_agent_adult}</td>
                                        <td>₹ {$tour_package_price_net_child}</td>
                                        <td>₹ {$tour_package_price_mrp_child}</td>
                                        <td>₹ {$tour_package_price_selling_child}</td>
                                        <td>₹ {$tour_package_price_agent_child}</td>
                                        <td>" . ucwords($tour_package_price_type) . "</td>
                                        <td>{$packageStatus}</td>
                                        <td>
                                            <a href='#' class='tableActionIconBtn editPrice' title='Edit price' data-id='{$tour_package_price_id}' data-title='Edit price'><i class='fi fi-rr-edit bgWhite'></i></a>
                                            <a href='#' data-type='packages/price' data-id='{$tour_package_price_id}' title='Delete price' data-title='Delete price' class='tableActionIconBtn deleteButton' data-reload='true' data-redirect='false'><i class='fi fi-rr-trash bgRed'></i></a>
                                        </td>
                                    </tr>
                                ";
                                $i++;
                            }
                        } else {
                            echo "<tr>
                            <td colspan=100>No Data Found</td>
                            </tr>";
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="popUp centerBodyPopUp editPriceBox">
    <div class="fullScreenPopUpBox">
        <div class="widget box-8">
            <div class="contentArea">
                <h4 class="heading" style="margin-bottom: 2rem">Search Itinenary</h4>
                <div class="box-1">
                    <form id="singleSharingForm">
                        <div class="priceBox">
                            <div class="form-group">
                                <div class="box-5">
                                    <p class="form-lable" id="Label">Starting date<sup>*</sup></p>
                                    <input id="startDate" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartSingleSharing" type="text" required />
                                </div>
                                <div class="box-5">
                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                    <input id="endDate" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndSingleSharing" type="text" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <h2>Price per Adult ( in ₹ )</h2>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                    <input id="adultNet" name="netCostAdultSingleSharing" placeholder="999" type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">MRP</p>
                                    <input id="adultMRP" name="mrpAdultSingleSharing" placeholder="1999" type="number" />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                    <input id="adultSelling" name="sellingPriceAdultSingleSharing" placeholder="1799" type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Sales team price</p>
                                    <input id="adultAgent" name="agentPriceAdultSingleSharing" placeholder="1499" type="number" />
                                </div>
                            </div>
                            <div class="form-group">
                                <h2>Price per Children ( in ₹ )</h2>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                    <input id="childNet" name="netCostChildSingleSharing" placeholder="999" type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">MRP</p>
                                    <input id="childMRP" name="mrpChildSingleSharing" placeholder="1999" type="number" />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                    <input id="childSelling" name="sellingPriceChildSingleSharing" placeholder="1799" type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Sales team price</p>
                                    <input id="childAgent" name="agentPriceChildSingleSharing" placeholder="1499" type="number" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                            <input type="hidden" name="priceType" value="single">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="widget box-2">
            <div class="actionArea">
                <a class="btn popUpBtn" id="updateItineraryPriceBtn"><i class="fi-rr-check"></i> Update price</a>
                <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</a>
            </div>
        </div>
    </div>
</div>