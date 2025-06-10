<?php

$redirectToMobileAuth = true;
require_once LOGIN_CHECK;
require_once 'header.php';

if ($checkPackage = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "visa_package INNER JOIN " . TABLE_PREFIX . "visa_country ON visa_country_id=visa_package_country_id WHERE visa_package_id = '" . base64_decode($package_id) . "' AND visa_package_site_id = " . SITE_ID . " AND visa_country_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($checkPackage) > 0) {
        extract(mysqli_fetch_assoc($checkPackage));
    }
}

?>
<main class="container mx-auto px-4 py-40">
    <div class="max-w-4xl mx-auto">

        <!-- Header icon -->

        <div class="flex justify-between items-center mb-12">
            <div class="flex-1 flex items-center" id="step1-indicator">
                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-medium">
                    1</div>
                <div class="flex-1 h-1 bg-primary mx-2"></div>
            </div>
            <div class="flex-1 flex items-center" id="step2-indicator">
                <div
                    class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-medium">
                    2</div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            </div>
            <div class="flex-1 flex items-center" id="step3-indicator">
                <div
                    class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-medium">
                    3</div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"></div>
            </div>
            <div class="flex items-center" id="step4-indicator">
                <div
                    class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-medium">
                    4</div>
            </div>
        </div>

        <!-- Form -->

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form id="visaApplicationProcess" enctype="multipart/form-data">
                <div class="step-content" id="step1">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold">Personal Information</h2>
                        <!-- <a
                            class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer saveAsDraft">
                            <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                            Save as Draft
                        </a> -->
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name<sup>*</sup></label>
                            <input type="text" name="firstName" id="firstName" placeholder="Avinash"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name<sup>*</sup></label>
                            <input type="text" name="lastName" id="lastName" placeholder="Kumar"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of
                                Birth<sup>*</sup></label>
                            <input id="dob" name="dob" type="date" placeholder="1995-01-01"
                                min="<?php echo date('Y-m-d', strtotime('-100 years')) ?>"
                                max="<?php echo date('Y-m-d') ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender<sup>*</sup></label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="male" class="mr-2" checked>
                                    Male
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="gender" value="female" class="mr-2">
                                    Female
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Passport
                                Number<sup>*</sup></label>
                            <input type="text" name="passportNumber" id="passportNumber" placeholder="A1234567"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Passport Expiry
                                Date<sup>*</sup></label>
                            <input id="passportExpiry" name="passportExpiry" type="date"
                                min="<?php echo date('Y-m-d', strtotime('+6 months')) ?>"
                                max="<?php echo date('Y-m-d', strtotime('+15 years')) ?>"
                                class=" w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email
                                Address<sup>*</sup></label>
                            <input type="email" name="email" id="email" placeholder="myemailaddress@gmail.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile
                                Number<sup>*</sup></label>
                            <input type="tel" id="mobile" name="mobile" placeholder="9999999999"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                                readonly>
                        </div>
                    </div>
                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Address<sup>*</sup></label>
                        <textarea id="address" name="address" placeholder="House no, area, city, state, pin code"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary h-24"></textarea>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <a id="step1Btn"
                            class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer">Next
                            Step</a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-content" style="display: none;" id="step2">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold">Travel Details</h2>
                        <!-- <a
                            class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer saveAsDraft">
                            <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                            Save as Draft
                        </a> -->
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departure
                                Date<sup>*</sup></label>
                            <input type="date" id="departureDate" name="departureDate" min="<?php echo date('Y-m-d') ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arrival Date<sup>*</sup></label>
                            <input type="date" id="arrivalDate" name="arrivalDate" min="<?php echo date('Y-m-d') ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of
                                Visit<sup>*</sup></label>
                            <select
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                                name="purposeOfTravel" id="purposeOfTravel">
                                <option value="">Select purpose</option>
                                <option value="tourism" selected>Tourism</option>
                                <option value="business">Business</option>
                                <option value="study">Study</option>
                                <option value="medical">Medical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of
                                Entries<sup>*</sup></label>
                            <select
                                class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                                id="visaEntry" name="visaEntry">
                                <option value="">Select entries</option>
                                <option value="single" selected>Single Entry</option>
                                <option value="multiple">Multiple Entry</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation
                            Details</label>
                        <textarea id="accommodation" name="accommodation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary h-24"
                            placeholder="Hotel name, address or host details"></textarea>
                    </div>
                    <div class="mt-8 flex justify-between">
                        <a id="goToStep1Btn"
                            class="text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer">Previous</a>
                        <a id="step2Btn"
                            class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer">Next
                            Step</a>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-content" style="display: none;" id="step3">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold">Document Upload</h2>
                        <!-- <a
                            class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer saveAsDraft">
                            <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                            Save as Draft
                        </a> -->
                    </div>
                    <div class="space-y-6">
                        <div class="border border-gray-300 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-medium">Passport</h3>
                                    <p class="text-sm text-gray-500">Upload clear scan of passport bio page</p>
                                </div>
                                <div class="text-sm text-gray-500">Max size: 5MB</div>
                            </div>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <input type="file" id="passportCopy" name="files[passportCopy]" aria-invalid="false"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    accept="image/*,.pdf">
                                <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
                                <div class="text-sm text-gray-600">Drag and drop or click to upload</div>
                            </div>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-medium">Passport Size Photo</h3>
                                    <p class="text-sm text-gray-500">Recent photo with white background</p>
                                </div>
                                <div class="text-sm text-gray-500">Max size: 2MB</div>
                            </div>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <input type="file" id="profilePhoto" name="files[profilePhoto]" aria-invalid="false"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
                                <div class="text-sm text-gray-600">Drag and drop or click to upload</div>
                            </div>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="font-medium">Supporting Documents</h3>
                                    <p class="text-sm text-gray-500">Bank statements, flight tickets, hotel bookings
                                        etc.</p>
                                </div>
                                <div class="text-sm text-gray-500">Max size: 10MB</div>
                            </div>
                            <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <input type="file" id="supportingDocuments" name="files[supportingDocuments]"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" aria-invalid="false"
                                    accept="image/*,.pdf" multiple>
                                <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
                                <div class="text-sm text-gray-600">Drag and drop or click to upload multiple files</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-between">
                        <a id="goToStep2Btn"
                            class="text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer">Previous</a>
                        <a id="step3Btn"
                            class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer">Next
                            Step</a>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-content" style="display: none;" id="step4">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-bold">Review & Payment</h2>
                    </div>
                    <div class="space-y-8">
                        <div class="border border-gray-200 rounded-lg divide-y">
                            <div class="p-6">
                                <h3 class="font-medium mb-4">Personal Information</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Full Name</div>
                                        <div class="font-medium" id="reviewFullName"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Date of Birth</div>
                                        <div class="font-medium" id="reviewDateOfBirth"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Passport Number</div>
                                        <div class="font-medium" id="reviewPassport"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Email</div>
                                        <div class="font-medium" id="reviewEmail" style="word-wrap: break-word;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-medium mb-4">Travel Details</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Travel Dates</div>
                                        <div class="font-medium" id="reviewTravelDates"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Country</div>
                                        <div class="font-medium" id="reviewCountry"><?php echo $visa_country_title ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Purpose</div>
                                        <div class="font-medium" id="reviewTourism"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Entry</div>
                                        <div class="font-medium" id="reviewEntry"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-medium mb-4">Documents</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Passport</div>
                                        <div class="font-medium">Uploaded</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Passport Photo</div>
                                        <div class="font-medium">Uploaded</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Supporting documents</div>
                                        <div class="font-medium" id="reviewSupportingDocuments"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="p-6">
                                <h3 class="font-medium mb-4">Payment Summary</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Visa Processing Fee</div>
                                        <div class="font-medium" id="processingFee"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Service Fee</div>
                                        <div class="font-medium" id="serviceFee"></div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Total Amount</div>
                                        <div class="font-medium" id="totalAmount"></div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col space-y-4 sm:flex-row sm:justify-between sm:space-y-0 sm:space-x-4">
                        <a id="goToStep3Btn"
                            class="w-full sm:w-auto text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer text-center">Previous</a>
                        <a id="submitVisaApplicationFormBtn"
                            class="w-full sm:w-auto bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer text-center">Submit
                            Application</a>
                        <!-- <a id="submitVisaApplicationFormBtn"
                            class="w-full sm:w-auto bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer text-center">Proceed
                            to pay</a> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
require_once 'footer.php';
?>