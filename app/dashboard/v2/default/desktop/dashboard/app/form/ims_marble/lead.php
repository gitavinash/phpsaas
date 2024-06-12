<section class="form">
    <div class="step-open-box step_1_box">
        <div class="form-header">
            <h1>Enter lead details</h1>
            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
        </div>
        <div class="form-group">
            <input id="firstName" name="name" type="text" required />
            <label class="form-lable" id="Label">Full name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="email" name="email" type="text" />
            <label class="form-lable" id="Label">Email</label>
        </div>
        <div class="form-group">
            <input id="mobile" name="mobile" type="text" required />
            <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
        </div>
        <div class="form-group">
            <textarea name="address" id="requirements" cols="30" rows="10"></textarea>
            <label class="form-lable" id="Label">Address<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="mobile" name="architecture" type="text" required />
            <label class="form-lable" id="Label">Architecture name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <input id="mobile" name="constrictctor" type="text" required />
            <label class="form-lable" id="Label">Constrictctor name<sup>*</sup></label>
        </div>
        <div class="form-group">
            <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
            <label class="form-lable" id="Label">Requirements and details</label>
        </div>
        <div class="box-1">
            <div class="form-group">
                <input type="submit" class="btn btn-green" id="addEnquiry" value="Add new lead">
            </div>
        </div>
    </div>
</section>