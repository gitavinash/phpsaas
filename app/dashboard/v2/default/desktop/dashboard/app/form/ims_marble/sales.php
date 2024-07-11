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
                    <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="mobile" name="mobile" type="number" />
                    <label class="form-lable" id="Label">Customer mobile<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="email" name="email" type="email" />
                    <label class="form-lable" id="Label">Customer email</label>
                </div>
                <div class="form-group">
                    <input id="name" name="name" type="text" />
                    <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                </div>
                <div class="box-1">
                    <div class="form-group">
                        <!-- <input type="submit" class="btn btn-green step-box" id="step_2" value="N new sale"> -->
                        <a class="btn btn-green step-box" id="step_2">Next, add sales</a>
                    </div>
                </div>
            </div>
            <div class="step-open-box step_2_box" style="display: none;">
                <div class="form-header">
                    <h1>Enter customer detailsds</h1>
                    <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                </div>

                <div class="form-group">
                    <input id="name" name="name" type="text" />
                    <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="mobile" name="mobile" type="number" />
                    <label class="form-lable" id="Label">Customer mobile<sup>*</sup></label>
                </div>
                <div class="form-group">
                    <input id="email" name="email" type="email" />
                    <label class="form-lable" id="Label">Customer email</label>
                </div>
                <div class="form-group">
                    <input id="name" name="name" type="text" />
                    <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                </div>
                <div class="box-1">
                    <div class="form-group">
                        <input type="submit" class="btn btn-green" id="addSale" value="Add new sale">
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>