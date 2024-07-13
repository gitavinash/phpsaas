<ul class="steps-tab">
    <li class="step-box active" id="step_1"><span>1</span>Customer details</li>
    <li class="step-box" id="step_2"><span>2</span>Sales details</li>
</ul>
<div class="steps-box">
    <form action="#" id="addNewInventoryForm" method="POST" enctype="multipart/form-data">
        <section class="form">
            <div class="step-open-box step_1_box">
                <div class="form-header">
                    <h1>Enter customer details</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>

                <div class="form-group">
                    <input id="name" name="name" type="text" />
                    <label class="form-lable" id="Label">Full name<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="mobile" name="mobile" type="number" />
                    <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="email" name="email" type="email" />
                    <label class="form-lable" id="Label">Email</label>
                </div>
                <div class="form-group">
                    <textarea name="address" id="address" cols="30" rows="10"></textarea>
                    <label class="form-lable" id="Label">Address<sup>*</sup></label>
                </div>
                <div class="box-1">
                    <div class="form-group">
                        <!-- <input type="submit" cla¯ss="btn btn-green step-box" id="step_2" value="N new sale"> -->
                        <a class="btn btn-green step-box" id="step_2">Next, add sales <i
                                class="fi fi-rr-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="step-open-box step_2_box" style="display: none;">
                <div class="form-header">
                    <h1>Enter product details</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>

                <div class="productBox">
                    <div class="form-group">
                        <input id="name" name="name" type="text" />
                        <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                    </div>
                </div>
                <div class="productBoxClone">
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <a class="btn btn-green step-box" id="step_1"><i class="fi fi-rr-arrow-left"></i> Back,
                            edit customer</a>
                    </div>
                </div>
                <div class="box-5">
                    <div class="form-group">
                        <a class="btn btn-green step-box" id="step_2">Next, add sales <i
                                class="fi fi-rr-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>