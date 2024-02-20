<h2 class="heading">Change password<span>Update account password</span></h2>
<div class="main-box">
    <section>
        <div class="steps-box">
            <form action="#" id="updatePassword">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="form-header">
                            <h1>Change password</h1>
                            <img src="<?php echo IMG_LIBRARY ?>storyset/domain-tlds.png" alt="Add new domain tld">
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="password" name="password" type="password" />
                            <label class="form-lable" id="passwordLabel">New password <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input class="form-control" id="confirmPassword" name="cnf_password" type="password" />
                            <label class="form-lable" id="confirmPasswordLabel">Confirm password <sup>*</sup></label>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="type" value="setting">
                            <input type="hidden" name="action" value="password">
                            <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>">
                            <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>">
                            <input type="submit" id="updatePasswordBtn" value='Change Password' class='btn btn-green'>
                        </div>
            </form>
    </section>
    </form>
</div>
</section>
</div>