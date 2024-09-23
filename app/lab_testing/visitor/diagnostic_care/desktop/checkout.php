<?php

if ($test_id == '') {
    header("Location: /", TRUE, 301);
    exit();
}

require_once 'header.php';

?>
<section class="hero side-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-7">
                <h2 class="isfont-40 text-white my-3 is-font600 text-center text-lg-start">
                    Book Appointment
                </h2>
            </div>
            <div class="col-12 col-lg-5 my-4 my-lg-0">
                <img src="<?php echo USER_IMAGES ?>appointment.png" alt=""
                    class="img-fluid d-none d-lg-block m-auto ms-lg-auto" />
            </div>
        </div>
    </div>
</section>

<section class="vartical-padding side-padding checkout">
    <div class="container">
        <div class="row vartical-padding align-items-top">
            <div class="col-12 col-lg-6">
                <div class="checkout-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $productId = base64_decode($_GET['test_id']);

                            $testPackage = '';
                            $testQuery = "SELECT product_id, product_brand_id, product_title, inventory_currency_symbol, inventory_mrp, inventory_price FROM " . TABLE_PREFIX . "product INNER JOIN " . TABLE_PREFIX . "inventory ON inventory_relation_id = product_id WHERE product_site_id = " . SITE_ID . " AND product_id IN ($productId) AND inventory_site_id = " . SITE_ID;

                            if ($checkTest = mysqli_query($connection, $testQuery)) {

                                $finalTestsName = '';
                                $i = 0;
                                if (mysqli_num_rows($checkTest) != 0) {
                                    while ($testList = mysqli_fetch_assoc($checkTest)) {
                                        extract($testList);
                                        $finalTestsName .= $product_title . ' | ' ?>

                                        <tr>
                                            <td class="is-color-blue is-font600 isfont-27">
                                                <?php echo ucwords($product_title) ?>
                                            </td>
                                            <td class="is-color-blue is-font600 isfont-18">
                                                <?php echo $inventory_currency_symbol . ' ' . $inventory_price; ?></td>
                                            <td class="text-center">
                                                <i class="bx bx-x-circle text-center fs-3 text-danger removeTest"
                                                    id_val="<?php echo $product_id ?>"></i>
                                            </td>
                                        </tr>
                            <?php
                                        $i++;
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No test found</td></tr>";
                                }
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="apointment-form">
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" name="name" class="form-control" id="fullName"
                                        placeholder="Your name *" required />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Email" />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="number" onwheel="this.blur()" name="mobile" class="form-control" placeholder="Mobile *"
                                        id="mobile" required />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <input type="text" name="pickup_date" class="form-control" id="pickupDate"
                                        placeholder="Test pick-up date *" required />
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <select name="pickup_time" class="form-control" id="pickupTime" required>
                                        <option value="" disabled selected>Test pick-up time *</option>
                                        <option value="06:00 AM">06:00 AM</option>
                                        <option value="06:30 AM">06:30 AM</option>
                                        <option value="07:00 AM">07:00 AM</option>
                                        <option value="07:30 AM">07:30 AM</option>
                                        <option value="08:00 AM">08:00 AM</option>
                                        <option value="08:30 AM">08:30 AM</option>
                                        <option value="09:00 AM">09:00 AM</option>
                                        <option value="09:30 AM">09:30 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="10:30 AM">10:30 AM</option>
                                        <option value="11:00 AM">11:00 AM</option>
                                        <option value="11:30 AM">11:30 AM</option>
                                        <option value="12:00 PM">12:00 PM</option>
                                        <option value="12:30 PM">12:30 PM</option>
                                        <option value="01:00 PM">01:00 PM</option>
                                        <option value="01:30 PM">01:30 PM</option>
                                        <option value="02:00 PM">02:00 PM</option>
                                        <option value="02:30 PM">02:30 PM</option>
                                        <option value="03:00 PM">03:00 PM</option>
                                        <option value="03:30 PM">03:30 PM</option>
                                        <option value="04:00 PM">04:00 PM</option>
                                        <option value="04:30 PM">04:30 PM</option>
                                        <option value="05:00 PM">05:00 PM</option>
                                        <option value="05:30 PM">05:30 PM</option>
                                        <option value="06:00 PM">06:00 PM</option>
                                        <option value="06:30 PM">06:30 PM</option>
                                        <option value="07:00 PM">07:00 PM</option>
                                        <option value="07:30 PM">07:30 PM</option>
                                        <option value="08:00 PM">08:00 PM</option>
                                        <option value="08:30 PM">08:30 PM</option>
                                        <option value="09:00 PM">09:00 PM</option>
                                        <option value="09:30 PM">09:30 PM</option>
                                        <option value="10:00 PM">10:00 PM</option>
                                        <option value="10:30 PM">10:30 PM</option>
                                    </select>
                                </div>
                            </div>

                            <div class=" col-12">
                                <textarea class="form-control" name="address" placeholder="Test collection address *"
                                    id="pickUpAddress" required></textarea>
                            </div>
                        </div>
                        <!-- <a href="/booking-process/?price=<?php echo $inventory_price ?>" type="submit"
                            class="btn btn-primary w-100 my-4 py-3">
                            Submit
                        </a> -->
                        <input type="hidden" name="type" value="enquiry">
                        <input type="hidden" name="action" value="new">
                        <input type="hidden" name="subject" value="New Booking for <?php echo $finalTestsName ?>">
                        <input type="hidden" name="productId" value="<?php echo $test_id ?>">
                        <input type="hidden" name="productType" value="<?php echo $checkout_type ?>">
                        <input type="hidden" name="sender"
                            value="<?php echo base64_encode("diagnosticcare.in@gmail.com") ?>">
                        <input type="hidden" name="cc"
                            value="<?php echo base64_encode("kushagra.catalyst@gmail.com,itssoftworld@gmail.com") ?>">
                        <input type="hidden" name="redirect_url"
                            value="/shipping/?test_id=<?php echo $test_id ?>&checkout_type=<?php echo $checkout_type ?>">
                        <input type="submit" class="btn btn-primary w-100 my-4 py-3" value="Book now">

                        <div>
                            <p class="isfont-12 text-center text-">
                                <b>By clicking, you agree to our</b>
                                <a href="/privacy-policy/" target="_blank">Privacy Policy</a> and
                                <a href="/terms-and-conditions/" target="_blank">Terms of Use</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>