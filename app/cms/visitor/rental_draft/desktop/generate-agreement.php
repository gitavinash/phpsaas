<?php

if (isset($request_id)) {
    if ($request_id != '') {
        if ($checkRequest = mysqli_query($connection, "SELECT enquiry_id FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='" . base64_decode($request_id) . "' AND enquiry_site_id=" . SITE_ID)) {
            if (mysqli_num_rows($checkRequest) != 0) {
                $validId = true;
            }
        }
    }
}

if (!isset($validId)) {
    header("Location: /", TRUE, 301);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once DEFAULT_META ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.4.2/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel="stylesheet" type="text/css" href="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo USER_STYLESHEET ?>style.min.css">
</head>

<body>
    <div class="container-fullwidth">
        <div class="row g-0">
            <div class="col-12 col-md-4">
                <aside class="d-flex flex-column justify-content-center align-items-center vh-100">
                    <div class="logo mb-5 text-center">
                        <img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>">
                    </div>
                    <div class="w-100 d-flex justify-content-center align-items-center flex-grow-1">
                        <form action="" class="w-100" id="agreementForm">
                            <!-- <div id="infoBox">
                            </div> -->
                            <div id="landlordBox">
                                <h2>Landlord details</h2>
                                <div class="landlordForm">
                                    <input type="text" class="form-control" name="landlord_name" id="landlordName"
                                        placeholder="Full Name*">
                                    <input type="text" class="form-control" name="landlord_father_name"
                                        id="landlordFatherName" placeholder="Father/Mother Name*">
                                    <input type="number" class="form-control" name="landlord_mobile" id="landlordPhone"
                                        placeholder="Phone Number*">
                                    <textarea name="landlord_address" class="form-control" id="landlordAddress"
                                        placeholder="Permanent Address*"></textarea>
                                    <input type="email" class="form-control" name="landlord_email" id="landlordEmail"
                                        placeholder="Email id*">
                                    <input type="text" class="form-control" name="landlord_pan_card"
                                        id="landlordPanCard" placeholder="PAN Card">
                                </div>
                                <div class="landlordCloneForm"></div>
                                <button id="tenate" class="btn btn-success w-100">Next, add tenate
                                    details</button>
                            </div>
                            <div id="tenateBox" style="display:none">
                                <h2>Tenate details</h2>
                                <div class="tenateForm">
                                    <input type="text" class="form-control" name="tenate_name" id="tenateName"
                                        placeholder="Full Name*">
                                    <input type="text" class="form-control" name="tenate_father_name"
                                        id="tenateFatherName" placeholder="Father/Mother Name*">
                                    <input type="number" class="form-control" name="tenate_mobile" id="tenatePhone"
                                        placeholder="Phone Number*">
                                    <textarea name="tenate_address" class="form-control" id="tenateAddress"
                                        placeholder="Permanent Address*"></textarea>
                                    <input type="email" class="form-control" name="tenate_email" id="tenateEmail"
                                        placeholder="Email id*">
                                    <input type="text" class="form-control" name="tenate_pan_card" id="tenatePanCard"
                                        placeholder="PAN Card">
                                </div>
                                <div class="tenateCloneForm"></div>
                                <button id="house" class="btn btn-success w-100">Next, add property
                                    details</button>
                            </div>
                            <div id="houseBox" style="display:none">
                                <h2>Property details</h2>
                                <select name="property_floor" class="form-control" id="propertyFloor">
                                    <option value="#">Floor No.*</option>
                                    <option value="G">G</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="21+">21+</option>
                                </select>
                                <select name="property_bhk" class="form-control" id="propertyBHK">
                                    <option value="#">Choose BHK*</option>
                                    <option value="1 RK">1 RK</option>
                                    <option value="1 BHK">1 BHK</option>
                                    <option value="1.5 BHK">1.5 BHK</option>
                                    <option value="2 BHK">2 BHK</option>
                                    <option value="2.5 BHK">2.5 BHK</option>
                                    <option value="3 BHK">3 BHK</option>
                                    <option value="3.5 BHK">3.5 BHK</option>
                                    <option value="4 BHK">4 BHK</option>
                                </select>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="propertyAddressCheck" checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Same as landlord address
                                    </label>
                                </div>
                                <textarea name="property_address" class="form-control" id="propertyAddress"
                                    placeholder="Property Address*"></textarea>
                                <button id="contract" class="btn btn-success w-100">Next, add contract details</button>
                            </div>
                            <div id="contractBox" style="display: none;">
                                <h2>Contract details</h2>
                                <input type="text" name="agreement_start_date" id="agreementStartDate"
                                    class="form-control datepicker" placeholder="Agreement start date*">
                                <input type="number" id="rentAmount" name="rent_amount" class="form-control"
                                    placeholder="Monthly rent*">
                                <input type="number" name="payment_date" id="paymentDate" class="form-control"
                                    placeholder="Rent payment date*">
                                <input type="number" name="security_amount" id="securityAmount" class="form-control"
                                    placeholder="Security Amount*">
                                <select name="notice_period" class="form-control" id="noticePeriod">
                                    <option value="#">Notice period*</option>
                                    <option value="1 month">1 Month</option>
                                    <option value="2 month">2 Month</option>
                                    <option value="3 month">3 Month</option>
                                </select>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rent_increase"
                                        id="rentIncrease" checked>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Increase rent after 11 Months
                                    </label>
                                </div>
                                <textarea name="comment" id="comment" class="form-control"
                                    placeholder="Any other clauses?"></textarea>
                                <input type="hidden" name="request_id" value="<?php echo $request_id ?>">
                                <button id="submitAgreementForm" class="btn btn-success w-100">Submit &
                                    Continue</button>
                            </div>
                        </form>
                    </div>

                    <footer>
                        <p class="creditOnForm">Design and Developed
                            by <a href="https://blumox.com" target="_blank"><strong>Blumox Technologies</strong></a>
                        </p>
                        </a></p>
                    </footer>
                </aside>
            </div>
            <div class="col-12 col-md-8 d-none d-sm-block">
                <main>
                    <div class="mainBox d-flex align-items-center">
                        <div class="w-100">
                            <h1 class="w-100">Rent Agreement</h1>
                            <p class="w-100">Your rent agreement is just a few clicks away. After providing the
                                following
                                information...</p>
                            <div class="row">
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>info-active.png" id="infoImg" alt="">
                                        <p>Basic info</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>landlord-active.png" id="landlordImg" alt="">
                                        <p>Lnadlord</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>tenate.png" id="tenateImg" alt="">
                                        <p>Tenate</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>house.png" id="houseImg" alt="">
                                        <p>Property</p>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="featureIcon dir-left">
                                        <img src="<?php echo USER_IMAGES ?>contract.png" id="contractImg" alt="">
                                        <p>Contract</p>
                                    </div>
                                </div>
                                <!-- <div class="col">
                                <div class="featureIcon dir-left">
                                    <img src="<?php echo USER_IMAGES ?>secure.png" alt="">
                                    <p>Secure</p>
                                </div>
                            </div> -->
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    <?php require_once NOTIFICATION . 'index.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
    <script src="<?php echo USER_JS ?>app.min.js"></script>
</body>

</html>