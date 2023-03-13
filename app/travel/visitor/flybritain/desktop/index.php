<?php

require_once 'header.php';

?>

<div class="bookingForm">
    <div class="container">
        <div class="searchForm">
            <div class="form">
                <div class="inputs">
                    <div class="row">
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
                                    <select class="passangerDropDown" id="passangerPopUp">
                                        <option value="">1 Passanger</option>
                                    </select>
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
                                <input type="checkbox" id="directFlight" value="true">
                                <label>Direct Flights</label>
                            </div>
                            <div class="form-group">
                                <select id="trip" class="darkBg">
                                    <option value="oneway">One way</option>
                                    <option value="roundtrip" selected>Round trip</option>
                                    <option value="multicity">Multi-city</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="class" class="darkBg">
                                    <option value="any">Any</option>
                                    <option value="business">Business</option>
                                    <option value="first_class">First class</option>
                                    <option value="economy" selected>Economy</option>
                                    <option value="premium_economy">Premium economy</option>
                                </select>
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