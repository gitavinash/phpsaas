<h2 class="heading"><?php echo 'Add New measurement' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Measurement details</li>
        </ul>
        <div class="steps-box">
            <form action="#" id="addNewMeasurementForm" method="POST">
                <section class="form">

                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter Measurement details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hotel.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input id="productType" name="productType" type="text" />
                            <label class="form-lable" id="Label">Product type<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <textarea name="description" id="description" cols="30" rows="10"></textarea>
                            <label class="form-lable" id="Label">About measurement</label>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" name="measurementType" id="measurementType">
                                <option value="mm">Millimeter</option>
                                <option value="cm" selected>Centimeter</option>
                                <option value="in">Inches</option>
                                <option value="ft">Feet</option>
                                <option value="mt">Meter</option>
                                <option value="mt (t)">Metric Ton</option>
                            </select>
                            <label class="form-lable" id="Label">Measurement<sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <select class="selectJS form-control" name="calculationType" id="calculationType">
                                <option value="sq. mm.">Square Millimeter</option>
                                <option value="sq. cm.">Square Centimeter</option>
                                <option value="sq. in.">Square Inches</option>
                                <option value="sq. ft." selected>Square Feet</option>
                                <option value="sq. mt.">Square Meter</option>
                                <option value="mt (t)">Metric Ton</option>
                            </select>
                            <label class="form-lable" id="Label">Calculation<sup>*</sup></label>
                        </div>
                        <div class="box-1">
                            <div class="form-group">
                                <input type="button" class="btn btn-green" id="addMeasurement"
                                    value="Add new measurement">
                            </div>
                        </div>
                    </div>

                </section>
            </form>
        </div>
    </section>
</div>