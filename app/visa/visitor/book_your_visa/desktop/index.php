<?php

    require_once 'header.php';

?>

<section class="heroSection container-fluid">
    <div class="row">
        <div class="col">
            <h1>Apply for Your Visa Hassle-Free<span>Quick, Secure & Reliable!</span></h1>
            <form>
                <select class="selectJS form-control selectDestination" name="state" id="state"
                    placeholder="Select your destination">
                    <option value="US" data-flag="https://flagcdn.com/w80/us.png">United States</option>
                    <option value="CA" data-flag="https://flagcdn.com/w80/ca.png">Canada</option>
                    <option value="GB" data-flag="https://flagcdn.com/w80/gb.png">United Kingdom</option>
                    <option value="AU" data-flag="https://flagcdn.com/w80/au.png">Australia</option>
                    <option value="AE" data-flag="https://flagcdn.com/w80/ae.png">United Arab Emirates</option>
                    <option value="DE" data-flag="https://flagcdn.com/w80/de.png">Germany</option>
                    <option value="NZ" data-flag="https://flagcdn.com/w80/nz.png">New Zealand</option>
                    <option value="SG" data-flag="https://flagcdn.com/w80/sg.png">Singapore</option>
                    <option value="SA" data-flag="https://flagcdn.com/w80/sa.png">Saudi Arabia</option>
                    <option value="QA" data-flag="https://flagcdn.com/w80/qa.png">Qatar</option>
                    <option value="MY" data-flag="https://flagcdn.com/w80/my.png">Malaysia</option>
                    <option value="TH" data-flag="https://flagcdn.com/w80/th.png">Thailand</option>
                    <option value="FR" data-flag="https://flagcdn.com/w80/fr.png">France</option>
                    <option value="IT" data-flag="https://flagcdn.com/w80/it.png">Italy</option>
                    <option value="NL" data-flag="https://flagcdn.com/w80/nl.png">Netherlands</option>
                    <option value="JP" data-flag="https://flagcdn.com/w80/jp.png">Japan</option>
                    <option value="KR" data-flag="https://flagcdn.com/w80/kr.png">South Korea</option>
                    <option value="PT" data-flag="https://flagcdn.com/w80/pt.png">Portugal</option>
                    <option value="ES" data-flag="https://flagcdn.com/w80/es.png">Spain</option>
                    <option value="ZA" data-flag="https://flagcdn.com/w80/za.png">South Africa</option>
                </select>
            </form>
            <p class="suggestions"><strong>Suggestions:</strong> <a href="#">United States</a>, <a href="#">United Arab
                    Emirates</a>, <a href="#">Canada</a>, <a href="#">Australia</a></p>
        </div>
        <div class=" col">
            <img src="<?php echo USER_IMAGES ?>passport.png" class="heroImage" alt="">
        </div>
    </div>
</section>

<section class="visa-steps py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">
            Get Your Visa in <span class="text-primary">3 Simple Steps</span>
        </h2>

        <div class="row justify-content-center">
            <!-- Step 1 -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon">1️⃣</div>
                    <h4 class="fw-semibold mt-3">Apply Online</h4>
                    <p>Select your destination and complete the application securely.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon">2️⃣</div>
                    <h4 class="fw-semibold mt-3">Submit Documents</h4>
                    <p>We review and process your application with accuracy.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-icon">3️⃣</div>
                    <h4 class="fw-semibold mt-3">Receive Your Visa</h4>
                    <p>Get approval and travel with confidence.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <a href="#" class="btn btn-primary btn-lg mt-4 px-4">Start Your Application</a>
    </div>
</section>

<section class="popularDestination container-fluid">
    <h2>Popular destinations<span>Explore the Most Sought-After Visa Destinations</span></h2>
    <div class="row">
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Dubai</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
        <div class="col-3">
            <div class="destinationWithPrice">
                <a href="">
                    <div class="bgImage">
                        <img src="https://mediaim.expedia.com/destination/9/cd8a3f3db7149b0ce36d052aea1182df.jpg" alt=""
                            title="">
                    </div>
                    <div class="destinationText">
                        <h3>Quick</h3>
                        <p><strong>Get your VISA by:</strong> <?php echo date('d M Y', strtotime('+7 days')) ?></p>
                    </div>
                    <div class="destinationFlag">
                        <img src="https://flagcdn.com/w80/ae.png" alt="" title="">
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="why-choose-us">
    <div class="container">
        <h2>Why Choose Us?<span>Your Trusted Partner for a Smooth Visa Experience</span></h2>
        <div class="row">
            <div class="col-3">
                <div class="feature">
                    <i class="fas fa-bolt"></i>
                    <h3>Quick Processing</h3>
                    <p>Get your visa approved quickly and easily.</p>
                </div>
            </div>
            <div class="col-3">
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure & Reliable</h3>
                    <p>Your data and application are in safe hands.</p>
                </div>
            </div>
            <div class="col-3">
                <div class="feature">
                    <i class="fas fa-user-tie"></i>
                    <h3>Expert Consultation</h3>
                    <p>Personalized assistance at every step.</p>
                </div>
            </div>
            <div class="col-3">
                <div class="feature">
                    <i class="fas fa-tags"></i>
                    <h3>Affordable Pricing</h3>
                    <p>Transparent fees with no hidden charges.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

    require_once 'footer.php';

?>