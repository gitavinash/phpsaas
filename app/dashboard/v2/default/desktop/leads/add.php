<h2 class="heading"><?php echo 'Add New lead' ?><span>Insert new lead into CRM</span>
</h2>
<div class="main-box">
    <section>
        <ul class="steps-tab">
            <li class="step-box active" id="step_1"><span>1</span>Basic details</li>
            <!-- <li class="step-box" id="step_2"><span>2</span>Domain validity</li> -->
        </ul>
        <div class="steps-box">
            <form action="#" method="POST" id="addNewForm">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Enter basic details</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/hosting.png" alt="Add new Hosting tld">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" name="first_name" type="text" required />
                            <label class="form-lable" id="nameLabel">
                                First name <sup>*</sup>
                            </label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" name="last_name" type="text" />
                            <label class="form-lable" id="nameLabel">
                                Last name <sup>*</sup>
                            </label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" name="email" type="text" />
                            <label class="form-lable" id="nameLabel">
                                Email <sup>*</sup>
                            </label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="name" name="mobile" type="text" required />
                            <label class="form-lable" id="nameLabel">
                                Mobile <sup>*</sup>
                            </label>
                        </div>
                        <div class="form-group">
                            <p>Tour package <sup>*</sup></p>
                            <select class="selectJS form-control" name="subject" id="select" required>
                                <option value=""></option>
                                <option value="Himachal tour package">Himachal tour package</option>
                                <option value="Manali tour package">Manali tour package</option>
                                <option value="Shimla tour package">Shimla tour package</option>
                                <option value="Kerala tour package">Kerala tour package</option>
                                <option value="Kashmir tour package">Kashmir tour package</option>
                                <option value="Rajastha tour package">Rajastha tour package</option>
                                <option value="Uttarakhand tour package">Uttarakhand tour package</option>
                                <option value="Chardham tour package">Chardham tour package</option>
                            </select>
                        </div>
                        <div class="box-2">
                            <div class="form-group">
                                <input type="hidden" name="type" value="enquiry">
                                <input type="hidden" name="action" value="new">
                                <input type="hidden" name="disableEmailNotification" value="true">
                                <input type="submit" class="btn btn-green" value="Add new lead">
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>