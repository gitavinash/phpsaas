<h2 class="heading"><?php echo 'Add New transporter' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Transporter details</li>
        </ul>
        <div class="steps-box">
            <form action="#" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter transporter details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="name" name="name" type="text" />
                            <label class="form-lable" id="Label">Transporter name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About transporter</label>
                        </div>
                        <div class="form-group">
                            <textarea name="address" id="address" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Address<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="city" name="city" type="text" />
                            <label class="form-lable" id="Label">Transporter city<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="pinCode" name="pinCode" type="number" />
                            <label class="form-lable" id="Label">Pin code<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="email" name="email" type="email" />
                            <label class="form-lable" id="Label">Email<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input id="mobile" name="mobile" type="number" />
                            <label class="form-lable" id="Label">Contact no.<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="service_area" id="serviceArea" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">Service area ( Enter multiple state name using , Ex:,
                                Himachal Pradesh, Punjab )</label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="submit" class="btn btn-green" id="addTransporter" value="Add new transporter">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>