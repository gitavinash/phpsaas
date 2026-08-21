<h2 class="heading"><?php echo 'Add New policy' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Policy details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewPolicyForm" method="POST">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter policy details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input name="title" id="title" type="text" />
                            <label class="form-lable" id="Label">Policy name<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <div class="editor" id="editor0" style="min-height: 500px"></div>
                            <textarea name="description" id="editor-0" style="display:none"></textarea>
                            <label class="form-lable" id="Label">Description<sup>*</sup></label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn btn-green" id="addPolicy">Add new policy</a>
                                <!-- <button type="submit" class="btn btn-green" id="addPolicy" value="Add new policy"></button> -->
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>