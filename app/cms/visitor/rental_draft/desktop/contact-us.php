<?php

require_once 'header.php';

?>

<div class="container">
    <div class="row">
        <div class="col">
            <section class="intro" id="getStartedSection" style="margin-bottom: 200px;">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h1 style="padding:0">Contact Us</h1>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Address:</strong><br />2, G-127, Gali no 4, Ashok Vihar Phase 3 Extention,
                                    Gurugram, Gurugram, Haryana, 122015</p>
                                <p><strong>Contact no.:</strong><br />+91-9013622555</p>
                                <p><strong>Email:</strong><br />support@rentaldraft.com, info@rentaldraft.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="startNowFormBox">
                            <div class="formBody">
                                <h2>Have a query or Feedback?</h2>
                                <p>Fill out the form below</p>
                                <form id="contactUsForm">
                                    <!-- <label for="name" class="form-label">Full name<sup>*</sup></label> -->
                                    <input type="text" name="name" class="form-control" id="name"
                                        placeholder="Full name" required>
                                    <!-- <label for="mobile" class="form-label">Contact number<sup>*</sup></label> -->
                                    <input type="number" name="mobile" class="form-control" id="mobile"
                                        placeholder="Mobile number" required>
                                    <!-- <label for="email" class="form-label">Email address</label> -->
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Email address">
                                    <textarea name="message" class="form-control" placeholder="Message"
                                        id=""></textarea>
                                    <input type="submit" class="btn btn-success w-100" value="Submit">
                                </form>
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