<h2 class="heading"><?php echo 'Edit ' . ucwords($urlStructure[1]) ?><span>Update tour package price</span>
</h2>
<div class="main-box">
    <div class="box-1">
        <!-- <div class="shortcut">
            <a href="#" id="openPackagePriceBox"><i class="fi fi-rr-plus"></i></a>
        </div> -->
        <?php
        if (isset($id)) {

            $tourPackageId = base64_decode($id);
            if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "tour_package LEFT JOIN " . TABLE_PREFIX . "tour_package_price ON tour_package_id=tour_package_price_parent_id WHERE tour_package_id='{$tourPackageId}' AND tour_package_site_id=" . SITE_ID)) {
                if (mysqli_num_rows($checkPackage) != 0) {
                    extract(mysqli_fetch_assoc($checkPackage)); ?>
                    <section>
                        <ul class="steps-tab">
                            <li class="step-box active" id="step_1"><span>1</span>Single sharing price</li>
                            <li class="step-box" id="step_2"><span>2</span>Double sharing price</li>
                            <li class="step-box" id="step_3"><span>3</span>Triple sharing price</li>
                            <li class="step-box" id="step_4"><span>4</span>Quad sharing price</li>
                        </ul>
                        <div class="steps-box">
                            <section class="form">
                                <div class="step-open-box step_1_box">
                                    <div class="form-header">
                                        <h3 style="float:left; width:100%">Single room sharing price <span>Make same date for if you want a same price for all time</span></h3>
                                    </div>
                                    <form id="singleSharingForm">
                                        <div class="priceBox">
                                            <div class="form-group">
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Starting date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartSingleSharing[]" type="text" required />
                                                </div>
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndSingleSharing[]" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Adult ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostAdultSingleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpAdultSingleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceAdultSingleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceAdultSingleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Children ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostChildSingleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpChildSingleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceChildSingleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceChildSingleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="priceContainer"></div>
                                        <div class="form-group">
                                            <a href="#" class="cloneSingleSharingPriceBox btn" style="float: right; width:50%"><i class="fi fi-rr-multiple"></i> Add more dates</a>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                                            <input type="hidden" name="priceType" value="single">
                                            <input type="button" class="btn saveItineraryPriceBtn" value="Update single sharing ininenary price">
                                        </div>
                                    </form>
                                </div>
                                <div class="step-open-box step_2_box" style="display: none;">
                                    <div class="form-header">
                                        <h1 style="float:left; width:100%">Double room sharing price</h1>
                                    </div>
                                    <form id="DoubleSharingForm">
                                        <div class="doublePriceBox">
                                            <div class="form-group">
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Starting date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartDoubleSharing[]" type="text" required />
                                                </div>
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndDoubleSharing[]" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Adult ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostAdultDoubleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpAdultDoubleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceAdultDoubleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceAdultDoubleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Children ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostChildDoubleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpChildDoubleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceChildDoubleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceChildDoubleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="doublePriceContainer"></div>
                                        <div class="form-group">
                                            <a href="#" class="cloneDoubleSharingPriceBox btn" style="float: right; width:50%"><i class="fi fi-rr-multiple"></i> Add more dates</a>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                                            <input type="hidden" name="priceType" value="double">
                                            <input type="button" class="btn saveItineraryPriceBtn" value="Update double sharing ininenary price">
                                        </div>
                                    </form>
                                </div>
                                <div class="step-open-box step_3_box" style="display: none;">
                                    <div class="form-header">
                                        <h1 style="float:left; width:100%">Triple room sharing price</h1>
                                    </div>
                                    <form id="TripleSharingForm">
                                        <div class="triplePriceBox">
                                            <div class="form-group">
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Starting date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartTripleSharing[]" type="text" required />
                                                </div>
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndTripleSharing[]" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Adult ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostAdultTripleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpAdultTripleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceAdultTripleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceAdultTripleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Children ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostChildTripleSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpChildTripleSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceChildTripleSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceChildTripleSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="triplePriceContainer"></div>
                                        <div class="form-group">
                                            <a href="#" class="cloneTripleSharingPriceBox btn" style="float: right; width:50%"><i class="fi fi-rr-multiple"></i> Add more dates</a>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                                            <input type="hidden" name="priceType" value="triple">
                                            <input type="button" class="btn saveItineraryPriceBtn" value="Update triple sharing ininenary price">
                                        </div>
                                    </form>
                                </div>

                                <div class="step-open-box step_4_box" style="display: none;">
                                    <div class="form-header">
                                        <h1 style="float:left; width:100%">Quad room sharing price</h1>
                                    </div>
                                    <form id="QuadSharingForm">

                                        <div class="quadPriceBox">
                                            <div class="form-group">
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Starting date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 July" name="sessionStartQuadSharing[]" type="text" required />
                                                </div>
                                                <div class="box-5">
                                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                                    <input id="name" class="datePickerWithoutYear" placeholder="15 August" name="sessionEndQuadSharing[]" type="text" required />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Adult ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostAdultQuadSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpAdultQuadSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceAdultQuadSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceAdultQuadSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Price per Children ( in ₹ )</h2>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                                    <input id="name" name="netCostChildQuadSharing[]" placeholder="999" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">MRP</p>
                                                    <input id="name" name="mrpChildQuadSharing[]" placeholder="1999" type="number" />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                                    <input id="name" name="sellingPriceChildQuadSharing[]" placeholder="1799" type="number" required />
                                                </div>
                                                <div class="box-2-5">
                                                    <p class="form-lable" id="Label">Sales team price</p>
                                                    <input id="name" name="agentPriceChildQuadSharing[]" placeholder="1499" type="number" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="quadPriceContainer"></div>
                                        <div class="form-group">
                                            <a href="#" class="cloneQuadSharingPriceBox btn" style="float: right; width:50%"><i class="fi fi-rr-multiple"></i> Add more dates</a>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="tourPackageId" value="<?php echo $id ?>">
                                            <input type="hidden" name="priceType" value="quad">
                                            <input type="button" class="btn saveItineraryPriceBtn" value="Update quad sharing ininenary price">
                                        </div>
                                    </form>
                                </div>
                            </section>
                        </div>
                    </section>

        <?php
                } else {
                    echo "Invalid request found";
                }
            } else {
                echo ((DEVELOPMENT_MODE == TRUE) ? mysqli_error($connection) : 'Something went wrong');
            }
        } else {
            echo "Required paramater is missing";
        }

        ?>
    </div>

</div>

<div class="popUp priceBoxPopUp">
    <div class="fullScreenPopUpBox">
        <div class="widget box-8">
            <div class="contentArea">
                <form method="POST" action="#">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h4 class="heading">Conclusion & Summary</h4>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" id="notesTitle" name="title">
                                <option value=""></option>
                                <option value="Not interested">Not Interested</option>
                                <option value="Purchased Elsewhere">Purchased Elsewhere</option>
                                <option value="Unqualified Lead">Unqualified Lead</option>
                                <option value="Fake Enquiry">Fake Enquiry</option>
                                <option value="Client is not Picking call">Client is not Picking call</option>
                                <option value="other">Other</option>
                            </select>
                            <label for="">Title <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea type="text" name="noteMsg" id="noteMsg" placeholder="Message*"
                                class="form-control" required></textarea>
                            <label class="form-lable" id="noteMsgLabel">Message</label>
                        </div>
                    </div>
            </div>
        </div>
        <div class="widget box-2">
            <div class="actionArea">
                <a class="btn popUpBtn" id="updateNotes"><i class="fi-rr-check"></i> Mark as closed</a>
                <span class="btn popUpBtn" onclick="closePopup(this)"><i class=" fi-rr-cross"></i>
                    Cancel</span>
            </div>
        </div>
    </div>
</div>