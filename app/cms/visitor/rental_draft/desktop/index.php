<?php

require_once 'header.php';

?>

<div class="container">
    <div class="row">
        <div class="col">
            <section class="intro">
                <div class="row">
                    <div class="col-6">
                        <h1>Create Your Rent Agreement Online</h1>
                        <div class="row">
                            <div class="col-4">
                                <div class="featureIcon dir-left">
                                    <img src="<?php echo USER_IMAGES ?>easy.png" alt="">
                                    <p>Easy</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="featureIcon dir-left">
                                    <img src="<?php echo USER_IMAGES ?>fast.png" alt="">
                                    <p>Fast</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="featureIcon dir-left">
                                    <img src="<?php echo USER_IMAGES ?>secure.png" alt="">
                                    <p>Secure</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="startNowFormBox">
                            <div class="formBody">
                                <h2>Start your rental agreement</h2>
                                <p>Fill out the form below</p>
                                <form id="getStartedForm">
                                    <label for="name" class="form-label">Full name<sup>*</sup></label>
                                    <input type="text" class="form-control" id="name" placeholder="Avinash kumar"
                                        required>
                                    <label for="mobile" class="form-label">Contact number<sup>*</sup></label>
                                    <input type="number" class="form-control" id="mobile" placeholder="9990026008"
                                        required>
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" placeholder="name@example.com">
                                    <input type="submit" id="getStarted" class="btn btn-success w-100"
                                        value="Get Started">
                                </form>
                            </div>
                            <div class="formFooter">
                                <p><strong>100% legal protection</strong></p>
                                <p>Our agreements are 100% legally valid, <a href='/terms-and-conditions/'>T&C Apply</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php

require_once 'footer.php';

?>