<?php

if (defined('CALENDER_START')) {

    $day = 1;
    $month = date('m', strtotime(CALENDER_START)) - 1;
    $year = date('Y', strtotime(CALENDER_START));
} else {

    $month = '';
    $year = '';
    $day = date('d');
}

include_once 'header.php';

?>

<!-- Booking form screen -->

<section class="pt-5 bookingFormScreen">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="headingDate text-center">
                    <h2 class="h4 pt-3 font-weight-bold">
                        Pangat Booking
                    </h2>
                    <p class="">Choose a date below to place a request for pangat</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-6">
                <div id="myCalendar" class="vanilla-calendar">
                    <div class="vanilla-calendar-header">
                        <button class="vanilla-calendar-btn" id="prearrow" disabled="">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <div class="vanilla-calendar-header__label" id="month"></div>
                        <button class="vanilla-calendar-btn" id="nexarrow">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="vanilla-calendar-week">
                        <span>S</span>
                        <span>M</span>
                        <span>T</span>
                        <span>W</span>
                        <span>T</span>
                        <span>F</span>
                        <span>S</span>
                    </div>
                    <div class="calendar-body-wraper d-none">
                        <ul class="calendar-dates vanilla-calendar-body m-0 p-0" id="calendardates">
                        </ul>
                    </div>
                    <div class="retryButton mb-3 mt-3 working text-center">
                        <!-- <p class="mb-1">Click to Load again</p> -->
                        <i class="fa fa-refresh display-4"></i>
                    </div>
                    <div class="dtl-box">

                        <!-- Request received -->

                        <span class="d-inline-block slot-booked-not-confirmed"></span>
                        <span>Request received</span>

                    </div>
                    <div class="dtl-box">
                        <!-- Date blocked by admin -->

                        <span class="d-inline-block date-blocked"></span>
                        <span>Blocked by admin</span>
                    </div>
                    <div class="dtl-box">
                        <!-- Slot available -->

                        <span class="d-inline-block date-available"></span>
                        <span>Available</span>
                    </div>
                    <div class="dtl-box" style="margin-bottom: 10px">
                        <!-- Date blocked -->

                        <span class="d-inline-block date-booked"></span>
                        <span>Booked</span>
                    </div>
                    <div class="dtl-box">
                        <p>
                            Note: In Case of any issue, please call : <a href="tel:+91-9821005828">+91-9821005828</a>,
                            <a href="tel:+91-9560849164">+91-9560849164</a>, <a
                                href="tel:011-23670506">011-23670506</a>, <a href="tel:011-47200965">011-47200965</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 pl-md-0 pr-md-0">
                <div class="no-booking">Booking Not Available / Pending for the <span></span></div>
                <div class="bookingSlots active pl-3 pr-3">
                    <div class="row">
                        <div class="col-md-4 pl-1 pr-1">
                            <div class="breakfast-btn com-sch bor-rounded" booking-for="breakfast">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="d-inline-block left-icon pl-1 pr-1">
                                        <i class="fa fa-utensils"></i>
                                    </div>
                                    <div class="d-inline-block pl-1 pr-1">
                                        <p class="sch mb-0 text-white" style="color: #333 !important">Breakfast</p>
                                        <p class="time-sch mb-0">8 AM - 9 AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pl-1 pr-1">
                            <div class="lunch-btn com-sch bor-rounded" booking-for="lunch">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="d-inline-block left-icon pl-1 pr-1">
                                        <i class="far fa-sun"></i>
                                    </div>
                                    <div class="d-inline-block pl-1 pr-1">
                                        <p class="sch mb-0 text-white" style="color: #333 !important">Lunch</p>
                                        <p class="time-sch mb-0">12 PM - 1 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 pl-1 pr-1">
                            <div class="dinner-btn com-sch bor-rounded" booking-for="dinner">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="d-inline-block left-icon pl-1 pr-1">
                                        <i class="far fa-moon" style="color: #fff"></i>
                                    </div>
                                    <div class="d-inline-block pl-1 pr-1">
                                        <p class="sch mb-0 text-white">Dinner</p>
                                        <p class="time-sch mb-0" style="color: #fff !important">8 PM - 9 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="forms-data">
                        <div class="bookedForText">
                            <div class="forbreakfast justify-content-between align-items-center">
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="sch mb-0"><i class="fa fa-cutlery"></i> Breakfast</p>
                                </div>
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="time-sch mb-0">13-10-2020</p>
                                </div>
                            </div>
                            <div class="forlunch justify-content-between align-items-center">
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="sch mb-0"><i class="fa fa-sun-o"></i> Lunch</p>
                                </div>
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="time-sch mb-0">13-10-2020</p>
                                </div>
                            </div>
                            <div class="fordinner justify-content-between align-items-center">
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="sch mb-0"><i class="fa fa-moon-o"></i> Dinner</p>
                                </div>
                                <div class="d-inline-block pl-1 pr-1">
                                    <p class="time-sch mb-0">13-10-2020</p>
                                </div>
                            </div>
                        </div>
                        <div class="booking-select text-danger d-none">Select a date and booking slot first.</div>
                        <div class="form-cont">
                            <input type="text" name="first_name" class="firstName inputClass reqInput"
                                placeholder="First Name*" required>
                            <p class="color-red">Name is required</p>
                        </div>
                        <div class="form-cont">
                            <input type="text" name="last_name" class="lastName inputClass" placeholder="Last Name">
                        </div>
                        <div class="form-cont">
                            <input type="email" name="email" class="emailName inputClass reqInput" placeholder="Email"
                                required>
                            <p class="color-red">Email is required</p>
                        </div>
                        <div class="form-cont">
                            <input type="number" onwheel="this.blur()" name="mobile" inputmode="numeric" pattern="[0-9]*"
                                class="mobName inputClass phoneVerify reqInput" placeholder="Mobile Number" required
                                min="10" max="10" pattern="[6-9]{1}[0-9]{9}"
                                title="Enter Your 10 Digit Mobile Number only">
                            <p class="color-red">Enter a valid mobile number</p>
                        </div>
                        <div class="form-cont mb-4">
                            <input type="checkbox" id="guest" name="box" class="inputBox" checked>
                            <spn class="d-inline-block">I will be present in pangat.</span>
                        </div>
                        <div id="checkBox" class="form-cont">
                            <label>Select no of guests</label>
                            <select name="guest" class="inputClass guestVal reqInput">
                                <option value="">Select number of guests</option>
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
                            </select>
                            <p class="color-red">Select number of guests</p>
                        </div>
                        <input type="hidden" name="acceptPayment" value="1" id="acceptPayment">
                        <input type="hidden" name="amount" value="1000" id="pangatAmount">
                        <button class="submit-data">Send request</button>
                        <div class="serverError d-none text-danger">Something went wrong. Please try again</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Booking confirmation screen -->

<section class="pt-5 bookingConfirmationScreen " style="display:none">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="headingDate text-center">
                    <h2 class="h4 pt-3 font-weight-bold">
                        Pangat Booking Confirmation
                    </h2>
                    <p class="">Please confirm your booking details</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8 col-xs-12 bookingConfirmation ">
                <div class="row">
                    <div class="col-4">
                        <p>Booking amount<br /><span style="font-size: 12px">( including Dakshina )</span></p>
                    </div>
                    <div class="col-8">
                        <p id="bookingAmount"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Name</p>
                    </div>
                    <div class="col-8">
                        <p id="nameConfirmation"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Email</p>
                    </div>
                    <div class="col-8">
                        <p id="emailConfirmation"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Mobile</p>
                    </div>
                    <div class="col-8">
                        <p id="mobileConfirmation"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Booking date</p>
                    </div>
                    <div class="col-8">
                        <p id="bookingDateConfirmation"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Booking time</p>
                    </div>
                    <div class="col-8">
                        <p id="bookingTimeConfirmation"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <p>Are you coming</p>
                    </div>
                    <div class="col-8">
                        <p id="comingConfirmation"></p>
                    </div>
                </div>
                <div class="row guestComing" style="display: none">
                    <div class="col-4">
                        <p>No of guest</p>
                    </div>
                    <div class="col-8">
                        <p id="guestConfirmation"></p>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 10px">
                    <div class="col-4">
                        <p>Donation amount</p>
                    </div>
                    <div class="col-8">
                        <input type="number" onwheel="this.blur()" name="donation" id="donationAmount" inputmode="numeric" pattern="[0-9]*"
                            class="form-control" placeholder="If you want to donate any amount.">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-4 col-xs-12">
                        <a href="#" id="editForm" class="btn btn-primary btn-block">Edit</a>
                    </div>
                    <div class="col-2" style="height: 10px"></div>
                    <div class="col-md-4 col-xs-12">
                        <input type="hidden" id="calenderMonthLimit" value="<?php echo CALENDER_MONTH_VISIBLE - 1 ?>">
                        <input type="submit" id="submitForm" class="btn btn-success btn-block" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<?php

include_once 'footer.php';

?>