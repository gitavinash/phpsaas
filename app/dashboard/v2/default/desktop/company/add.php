<h2 class="heading"><?php echo 'Add New company' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Company details</li>
        </ul>
        <div class="steps-box">
            <form action="#" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter company details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Company name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About company</label>
                        </div>
                        <div class="form-group">
                            <textarea name="address" id="address" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <p>State<sup>*</sup></p>
                            <select class="selectJS form-control" name="state" id="state">
                                <?php echo $stateDropDownList ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input id="city" name="city" type="text" />
                            <label class="form-lable" id="Label">City<sup>*</sup></label>
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
                            <input id="mapUrl" name="cin" type="text" />
                            <label class="form-lable" id="Label">Registration no. ( CIN )</label>
                        </div>
                        <div class="form-group">
                            <input id="mapUrl" name="gst" type="text" />
                            <label class="form-lable" id="Label">GST Number</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="submit" class="btn btn-green" id="addCompany" value="Add new company">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>