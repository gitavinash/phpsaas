<?php
require_once 'header.php';
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
                <!-- Step 4 -->
                <div class="step-content" id="step4">
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