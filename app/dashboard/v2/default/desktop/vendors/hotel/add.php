<h2 class="heading"><?php echo 'Add New hotel' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Hotel details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
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