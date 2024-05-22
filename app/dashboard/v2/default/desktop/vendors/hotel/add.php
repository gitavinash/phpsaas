<h2 class="heading"><?php echo 'Add New hotel' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Hotel details</li>
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewHotel">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter hotel details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Hotel name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About hotel<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="address" id="address" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="city" name="city" type="text" />
                            <label class="form-lable" id="Label">Hotel city<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" type="number" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" type="text" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="number" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>Hotel type</p>
                            <select class="selectJS form-control" name="type" id="type">
                                <option value=""></option>
                                <option value="apartments">Apartments</option>
                                <option value="capsule hotel">Capsule hotel</option>
                                <option value="guest house">Guest house</option>
                                <option value="inn">Inn</option>
                                <option value="hostel">Hostel</option>
                                <option value="house">House</option>
                                <option value="home stay">Home stay</option>
                                <option value="1 star hotel">1 Star Hotel ( Tourist )</option>
                                <option value="2 star hotel">2 Star Hotel ( Standard )</option>
                                <option value="3 star hotel" selected>3 Star Hotel ( Comfort )</option>
                                <option value="4 star hotel">4 Star Hotel ( First Class )</option>
                                <option value="5 star hotel">5 Star Hotel ( Luxury )</option>
                                <option value="motel">Motel</option>
                                <option value="lodge">Lodge</option>
                                <option value="tent">Tent</option>
                                <option value="farm stay">Farm stay</option>
                                <option value="resorts">Resorts</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="mapUrl" name="mapUrl" type="text" />
                            <label class="form-lable" id="Label">Google map url</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="submit" class="btn btn-green" id="addHotel" value="Add new hotel">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>