<script>
for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (key && key.startsWith('packageInfo_')) {
        localStorage.removeItem(key);
    }
}
</script>
<h2 class="heading">Hotel list<span>All hostings list will be displayed here.</span>
</h2>
<div class="main-box">
    <div class="shortcut">
        <a href="/dashboard/packages/domestic/hotel/?id=<?php echo $id ?>" data-title="Add new price"><i
                class="fi fi-rr-plus"></i></a>
    </div>
    <section>
        <div class="list-box">
            <table class="showTableList largeThead">

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
                                    <input id="startDate" class="datePickerWithoutYear" placeholder="15 July"
                                        name="sessionStartSingleSharing" type="text" required />
                                </div>
                                <div class="box-5">
                                    <p class="form-lable" id="Label">Ending Date<sup>*</sup></p>
                                    <input id="endDate" class="datePickerWithoutYear" placeholder="15 August"
                                        name="sessionEndSingleSharing" type="text" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <h2>Price per Adult ( in ₹ )</h2>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                    <input id="adultNet" name="netCostAdultSingleSharing" placeholder="999"
                                        type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">MRP</p>
                                    <input id="adultMRP" name="mrpAdultSingleSharing" placeholder="1999"
                                        type="number" />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                    <input id="adultSelling" name="sellingPriceAdultSingleSharing" placeholder="1799"
                                        type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Sales team price</p>
                                    <input id="adultAgent" name="agentPriceAdultSingleSharing" placeholder="1499"
                                        type="number" />
                                </div>
                            </div>
                            <div class="form-group">
                                <h2>Price per Children ( in ₹ )</h2>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Net cost<sup>*</sup></p>
                                    <input id="childNet" name="netCostChildSingleSharing" placeholder="999"
                                        type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">MRP</p>
                                    <input id="childMRP" name="mrpChildSingleSharing" placeholder="1999"
                                        type="number" />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Selling price<sup>*</sup></p>
                                    <input id="childSelling" name="sellingPriceChildSingleSharing" placeholder="1799"
                                        type="number" required />
                                </div>
                                <div class="box-2-5">
                                    <p class="form-lable" id="Label">Sales team price</p>
                                    <input id="childAgent" name="agentPriceChildSingleSharing" placeholder="1499"
                                        type="number" />
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