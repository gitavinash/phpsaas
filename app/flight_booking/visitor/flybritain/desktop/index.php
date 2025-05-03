<?php

require_once 'header.php';

?>

<div class="bookingForm">
    <div class="container">
        <div class="searchForm">
            <div class="tabs">
                <a class="active">Flights</a>
                <a>Hotels</a>
            </div>
            <div class="form">
                <div class="inputs">
                    <div class="row ">
                        <div class="mainInputs">
                            <div class="form-group">
                                <div class="icon">
                                    <img src="<?php echo USER_IMAGES ?>icons/location.png" alt="">
                                </div>
                                <div class="box">
                                    <label>Leaving from</label>
                                    <input type="text" id="from" placeholder="City or Airport code">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="icon">
                                    <img src="<?php echo USER_IMAGES ?>icons/location.png" alt="">
                                </div>
                                <div class="box">
                                    <label>Going to</label>
                                    <input type="text" id="to" placeholder="City or Airport code">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="icon">
                                    <img src="<?php echo USER_IMAGES ?>icons/seat.png" alt="">
                                </div>
                                <div class="box">
                                    <label>Passangers</label>
                                    <input type="text" id="passangerPopUp" placeholder="1 Passanger" readonly>
                                    <div class="passangerPopUp formPopUp">
                                        <div class="numSec">
                                            <p></p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="icon">
                                    <img src="<?php echo USER_IMAGES ?>icons/calendar-orange.png" alt="">
                                </div>
                                <div class="box">
                                    <label>Depart & Return Date</label>
                                    <input type="text" class="returnDatePicker" id="departureDate" placeholder="Departure & Return date">
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <div class="icon">
                                    <img src="<?php echo USER_IMAGES ?>icons/calendar-orange.png" alt="">
                                </div>
                                <div class="box">
                                    <label>Return</label>
                                    <input type="text" class="returnDatePicker" id="returnDate"
                                        placeholder="Return date">
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="secondaryInputs">
                            <div class="form-group">
                                <label class="container"> Direct flights
                                    <input type="checkbox" id="directFlight" value="true">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <div class="box">
                                    <input type="text" id="tripPopUp" placeholder="Round trip" readonly>
                                    <div class="tripPopUp formPopUp">
                                        <ul class="dropDownList">
                                            <li>One way</li>
                                            <li>Round trip</li>
                                            <li>Multi city</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" id="classPopUp" placeholder="Economy" readonly>
                                <div class="classPopUp formPopUp">
                                    <ul class="dropDownList">
                                        <li>Any</li>
                                        <li>Business</li>
                                        <li>First class</li>
                                        <li>Economy</li>
                                        <li>Premium economy</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="searchBtnBox">
                    <button id="searchFlight">Search</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="gradientSection">
</div>
<section>
    <h3>Have your travel plans been affected?</h3>
    <div class="container">
        <div class="row flex-3">
            <div class="info-box">
                <div class="info">
                    <div class="img"></div>
                    <p>Why you should book with us?</p>
                </div>
            </div>
            <div class="info-box">
                <div class="info">
                    <div class="img"></div>
                    <p>Best fare guaranteed - Safe & Secure Booking</p>
                </div>
            </div>
            <div class="info-box">
                <div class="info">
                    <div class="img"></div>
                    <p>24/7 Customer Care</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row">
            <div class="slides">
                <h1>Popular Destinations</h1>
                <div class="slider">
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="text">flights from</div>
                            <div class="price">£ 9956</div>
                        </div>
                    </div>
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="priceInfo">
                                <div class="text">flights from</div>
                                <div class="price">£356</div>
                            </div>
                        </div>
                    </div>
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="priceInfo">
                                <div class="text">flights from</div>
                                <div class="price">£356</div>
                            </div>
                        </div>
                    </div>
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="priceInfo">
                                <div class="text">flights from</div>
                                <div class="price">£356</div>
                            </div>
                        </div>
                    </div>
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="priceInfo">
                                <div class="text">flights from</div>
                                <div class="price">£356</div>
                            </div>
                        </div>
                    </div>
                    <div class="slideBox">
                        <div class="bgImage">
                            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/17/02/98/0d/elephant-in-tarangire.jpg?w=1200&h=-1&s=1" alt="">
                        </div>
                        <div class="text">
                            <a href="">
                                <h2>Africa</h2>
                            </a>
                        </div>
                        <div class="priceBox">
                            <div class="priceInfo">
                                <div class="text">flights from</div>
                                <div class="price">£356</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php'

?>