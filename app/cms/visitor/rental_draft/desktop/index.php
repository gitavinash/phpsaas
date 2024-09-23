<?php

require_once 'header.php';

?>

<div class="container">
    <div class="row">
        <div class="col">
            <section class="intro" id="getStartedSection">
                <div class="row">
                    <div class="col-12 col-md-6">
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
                    <div class="col-12 col-md-6">
                        <div class="startNowFormBox">
                            <div class="formBody">
                                <h2>Start your rental agreement</h2>
                                <p>Fill out the form below</p>
                                <form id="getStartedForm">
                                    <!-- <label for="name" class="form-label">Full name<sup>*</sup></label> -->
                                    <input type="text" name="name" class="form-control" id="name"
                                        placeholder="Full name" required>
                                    <!-- <label for="mobile" class="form-label">Contact number<sup>*</sup></label> -->
                                    <input type="number" onwheel="this.blur()" name="mobile" class="form-control" id="mobile"
                                        placeholder="Mobile number" required>
                                    <!-- <label for="email" class="form-label">Email address</label> -->
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Email address">
                                    <!-- <label for="email" class="form-label">State</label> -->
                                    <!-- <input type="text" name="state" class="form-control" id="state" placeholder="State"> -->
                                    <select name="state" class="form-control" id="state">
                                        <?php

                                        if ($stateDetails = mysqli_query($connection, "SELECT state_id, state_name FROM " . TABLE_PREFIX . "state WHERE state_country_id=101")) {

                                            if (mysqli_num_rows($stateDetails) != 0) {

                                                echo "<option value='' >Select state</option>";

                                                while ($stateData = mysqli_fetch_assoc($stateDetails)) {

                                                    echo "<option value='{$stateData['state_name']}' " . (($stateData['state_name'] == $state_id) ? 'selected' : '') . " >{$stateData['state_name']}</option>";
                                                }
                                            } else {

                                                echo "<option value=''>No city found</option>";
                                            }
                                        }

                                        ?>
                                    </select>
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

<!-- How it's work -->

<div class="container">
    <div class="row">
        <div class="col">
            <section class="howItWorks dir-left">
                <h2>Why Choose Us?</h2>
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="feature-card d-flex flex-column">
                            <div>
                                <img src="<?php echo USER_IMAGES ?>quick_and_easy.jpg" alt="Quick and Easy">
                                <h3>Quick and Easy</h3>
                                <p>Create your agreement in just a few minutes.</p>
                            </div>
                            <?php

                            if (SITE_VERSION == 'mobile') { ?>
                                <div class="mt-auto">
                                    <a href="#getStartedSection" class="btn btn-success">Get Started</a>
                                </div>
                            <?php
                            }

                            ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="feature-card d-flex flex-column">
                            <div>
                                <img src="<?php echo USER_IMAGES ?>legally_complian.jpg" alt="Legally Compliant">
                                <h3>Legally Compliant</h3>
                                <p>All agreements are compliant with local laws.</p>
                            </div>
                            <?php

                            if (SITE_VERSION == 'mobile') { ?>
                                <div class="mt-auto">
                                    <a href="#getStartedSection" class="btn btn-success">Get Started</a>
                                </div>
                            <?php
                            }

                            ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="feature-card d-flex flex-column">
                            <div>
                                <img src="<?php echo USER_IMAGES ?>secur_digital_signatures.jpg"
                                    alt="Secure Digital Signatures">
                                <h3>Secure Digital Signatures</h3>
                                <p>Sign your agreement securely online.</p>
                            </div>
                            <?php

                            if (SITE_VERSION == 'mobile') { ?>
                                <div class="mt-auto">
                                    <a href="#getStartedSection" class="btn btn-success">Get Started</a>
                                </div>
                            <?php
                            }

                            ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="feature-card d-flex flex-column">
                            <div>
                                <img src="<?php echo USER_IMAGES ?>affordable_pricing.jpg" alt="Affordable Pricing">
                                <h3>Affordable Pricing</h3>
                                <p>Choose a plan that fits your needs and budget.</p>
                            </div>
                            <?php

                            if (SITE_VERSION == 'mobile') { ?>
                                <div class="mt-auto">
                                    <a href="#getStartedSection" class="btn btn-success">Get Started</a>
                                </div>
                            <?php
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- FAQ's -->

<div class="container">
    <div class="row">
        <div class="col">
            <section class="faqSection dir-left">
                <h2 class="text-center">Frequently Asked Questions ( FAQ's )</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What is a rent agreement?
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                A rent agreement is a legally binding document that outlines the terms and conditions of
                                a rental arrangement between a landlord and a tenant.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How can I create a rent agreement online?
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Creating a rent agreement online is simple. Just fill out our form with the necessary
                                details, and our system will generate a legally compliant agreement for you to sign
                                digitally.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Are digital signatures legally valid?
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, digital signatures are legally valid in most jurisdictions. They are recognized as
                                a secure and efficient way to sign documents online.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How do I know my information is secure?
                            </button>
                        </h3>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We use advanced encryption and security protocols to ensure that your information is
                                protected at all times. Your privacy and security are our top priorities.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                What are the costs involved?
                            </button>
                        </h3>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Our pricing plans are designed to be affordable and flexible. You can choose a plan that
                                fits your needs and budget. Visit our pricing page for more details.
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