<?php

require_once 'header.php';

?>
<div class="d-flex justify-content-center align-items-center" style="background: #f8f8f8; padding-bottom: 50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="onboarding">
                    <?php

                    if (isset($setup_id)) { ?>
                    <h2 class="text-center">Travel Agent registration form</h2>
                    <form id="kycFrom">
                        <!-- Section 1 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Company Information<sup>*</sup></h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">Legal Name</label>
                                        <input type="text" id="companyName" name="companyName" class="form-control"
                                            id="firstName" placeholder="Musafir Go India">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Established from</label>
                                        <input type="text" id="year" name="year" class="form-control" id="lastName"
                                            placeholder="2010">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="gst" class="form-label">Registration type</label>
                                        <select class="form-select" id="companyType" name="registration_type">
                                            <option value="">Select registration type</option>
                                            <option value="private">Private Limited Company</option>
                                            <option value="public">Public Limited Company</option>
                                            <option value="partnerships">Partnerships</option>
                                            <option value="llp">LLP Registration</option>
                                            <option value="opc">One Person Company</option>
                                            <option value="proprietorship">Sole Proprietorship</option>
                                            <option value="section8">Section 8 Company</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gst" class="form-label">Company category</label>
                                        <select class="form-select" name="companyType" id="tourismCategory">
                                            <option value="">Select company category</option>
                                            <option value="travel_agency">Travel Agencies</option>
                                            <option value="tour_operator">Tour Operators</option>
                                            <option value="hotels_accommodation">Hotels and Accommodation</option>
                                            <option value="transport_services">Transport Services</option>
                                            <option value="attractions_activities">Attractions and Activities
                                            </option>
                                            <option value="cruise_services">Cruise Services</option>
                                            <option value="events_conferences">Events and Conferences</option>
                                            <option value="travel_technology">Travel Technology</option>
                                            <option value="guided_tours">Guided Tours</option>
                                            <option value="travel_insurance">Travel Insurance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="city" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="companyEmail" id="email"
                                        placeholder="contact@aleya.in">
                                </div>
                                <div class="mb-3">
                                    <label for="city" class="form-label">Mobile number</label>
                                    <input type="number" class="form-control" name="companyMobile" id="mobile"
                                        placeholder="9355239595">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Address Details<sup>*</sup></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" name="companyAddress" id="address"
                                        placeholder="212A, Janak Puri">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" name="companyCity" id="city"
                                            placeholder="New Delhi">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <select class="form-select" name="companyState" id="state">
                                            <option value="">Select state</option>
                                            <option value="andaman_nicobar_islands">Andaman and Nicobar Islands</option>
                                            <option value="andhra_pradesh">Andhra Pradesh</option>
                                            <option value="arunachal_pradesh">Arunachal Pradesh</option>
                                            <option value="assam">Assam</option>
                                            <option value="bihar">Bihar</option>
                                            <option value="chandigarh">Chandigarh</option>
                                            <option value="chhattisgarh">Chhattisgarh</option>
                                            <option value="dadra_nagar_haveli_daman_diu">Dadra and Nagar Haveli and
                                                Daman and Diu</option>
                                            <option value="delhi">Delhi</option>
                                            <option value="goa">Goa</option>
                                            <option value="gujarat">Gujarat</option>
                                            <option value="haryana">Haryana</option>
                                            <option value="himachal_pradesh">Himachal Pradesh</option>
                                            <option value="jharkhand">Jharkhand</option>
                                            <option value="jammu_kashmir">Jammu and Kashmir</option>
                                            <option value="karnataka">Karnataka</option>
                                            <option value="kerala">Kerala</option>
                                            <option value="lakshadweep">Lakshadweep</option>
                                            <option value="ladakh">Ladakh</option>
                                            <option value="manipur">Manipur</option>
                                            <option value="meghalaya">Meghalaya</option>
                                            <option value="mizoram">Mizoram</option>
                                            <option value="nagaland">Nagaland</option>
                                            <option value="odisha">Odisha</option>
                                            <option value="puducherry">Puducherry</option>
                                            <option value="punjab">Punjab</option>
                                            <option value="rajasthan">Rajasthan</option>
                                            <option value="sikkim">Sikkim</option>
                                            <option value="tamil_nadu">Tamil Nadu</option>
                                            <option value="telangana">Telangana</option>
                                            <option value="tripura">Tripura</option>
                                            <option value="uttar_pradesh">Uttar Pradesh</option>
                                            <option value="uttarakhand">Uttarakhand</option>
                                            <option value="west_bengal">West Bengal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="zip" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control" name="companyPinCode" id="zip"
                                            placeholder="110058">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Business Documents<sup>*</sup></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="gst" class="form-label">GST Number</label>
                                    <input type="text" class="form-control" name="gstNumber" id="gstNo"
                                        placeholder="GST number">
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" id="gstCertificate" class="form-control"
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" name="gst_certificate">
                                    <label class="input-group-text">Upload GST Certificate</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="gstComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="gst" class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" name="panNumber" id="panNo"
                                        placeholder="GST number">
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" id="pan" class="form-control"
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" name="pan_card">
                                    <label class="input-group-text">Upload PAN Card</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="panComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="gst" class="form-label">Aadhar Number</label>
                                    <input type="text" class="form-control" name="aadharNumber" id="aadharNo"
                                        placeholder="GST number">
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" id="aadharCard" class="form-control" accept="image/*,.pdf"
                                        name="aadhar_card">
                                    <label class="input-group-text">Upload Aadhar Card</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="aadharComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="uploadDocument" value="true">
                        <input type="hidden" name="setupId" value="<?php echo $setup_id ?>">
                        <button id="submitKYC" class="btn btn-primary btn-lg">Submit request</button>
                    </form>

                    <?php
                    } else {
                        echo "<h2 class='text-center'>Invalid request found</h2>";
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';

?>