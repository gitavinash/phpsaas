<?php

require_once 'header.php';

?>

<div class="d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="onboarding">
                    <h2 class="text-center">Travel Agent registration form</h2>
                    <form>
                        <!-- Section 1 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Company Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">Legal Name<sup>*</sup></label>
                                        <input type="text" class="form-control" id="firstName"
                                            placeholder="Musafir Go India">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Established from<sup>*</sup></label>
                                        <input type="text" class="form-control" id="lastName" placeholder="2010">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="gst" class="form-label">GST Number<sup>*</sup></label>
                                    <input type="text" class="form-control" id="gst" placeholder="GST number">
                                </div>
                                <div class="mb-3">
                                    <label for="gst" class="form-label">Registration type<sup>*</sup></label>
                                    <select class="form-select" name="registration_type">
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
                            </div>
                        </div>

                        <!-- Section 2 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Address Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address<sup>*</sup></label>
                                    <input type="text" class="form-control" id="address" placeholder="212A, Janak Puri">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" placeholder="New Delhi">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" placeholder="Delhi">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="zip" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control" id="zip" placeholder="110058">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3 -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Business Documents</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="gstDocument">
                                    <label class="input-group-text">Upload GST Certificate</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="gstComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="panDocument">
                                    <label class="input-group-text">Upload PAN Card</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="panComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="gstDocument">
                                    <label class="input-group-text">Upload Aadhar Card</label>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Document password ( if any )</label>
                                    <textarea class="form-control" name="aadharComment" rows="3"
                                        placeholder="Your comments..."></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

require_once 'footer.php';

?>