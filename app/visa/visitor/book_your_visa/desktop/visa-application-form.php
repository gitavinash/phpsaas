<?php

require_once 'header.php';

?>
<main class="container mx-auto px-4 py-40">
    <div class="max-w-4xl mx-auto">
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
            <div class="flex items-center">
                <div
                    class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-medium">
                    4</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="step-content" style="display: <?php echo (($step == 1) ? 'block' : 'none') ?>" id=" step1">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold">Personal Information</h2>
                    <button class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer"
                        id="saveAsDraft">
                        <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                        Save as Draft
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="male" class="mr-2">
                                Male
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="female" class="mr-2">
                                Female
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number</label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Expiry Date</label>
                        <input type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                </div>
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Address</label>
                    <textarea
                        class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary h-24"
                        required></textarea>
                </div>
                <div class="mt-8 flex justify-end">
                    <button
                        class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer"
                        onclick="nextStep(2)">Next Step</button>
                </div>
            </div>
            <div class="step-content" style="display: <?php echo (($step == 2) ? 'block' : 'none') ?>" id="step2">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold">Travel Details</h2>
                    <button class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer"
                        id="saveAsDraft2">
                        <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                        Save as Draft
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Arrival Date</label>
                        <input type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departure Date</label>
                        <input type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                            <option value="">Select purpose</option>
                            <option value="tourism">Tourism</option>
                            <option value="business">Business</option>
                            <option value="study">Study</option>
                            <option value="medical">Medical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Entries</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary"
                            required>
                            <option value="">Select entries</option>
                            <option value="single">Single Entry</option>
                            <option value="multiple">Multiple Entry</option>
                        </select>
                    </div>
                </div>
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation Details</label>
                    <textarea
                        class="w-full px-4 py-2 border border-gray-300 rounded-button focus:outline-none focus:border-primary h-24"
                        placeholder="Hotel name, address or host details" required></textarea>
                </div>
                <div class="mt-8 flex justify-between">
                    <button
                        class="text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer"
                        onclick="prevStep(1)">Previous</button>
                    <button
                        class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer"
                        onclick="nextStep(3)">Next Step</button>
                </div>
            </div>
            <div class="step-content" style="display: <?php echo (($step == 3) ? 'block' : 'none') ?>" id="step3">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold">Document Upload</h2>
                    <button class="text-primary hover:text-primary/80 font-medium flex items-center cursor-pointer"
                        id="saveAsDraft3">
                        <i class="ri-save-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                        Save as Draft
                    </button>
                </div>
                <div class="space-y-6">
                    <div class="border border-gray-300 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-medium">Passport Copy</h3>
                                <p class="text-sm text-gray-500">Upload clear scan of passport bio page</p>
                            </div>
                            <div class="text-sm text-gray-500">Max size: 5MB</div>
                        </div>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/*,.pdf" required>
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
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/*" required>
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
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                accept="image/*,.pdf" multiple required>
                            <i class="ri-upload-cloud-line text-gray-400 text-3xl mb-2"></i>
                            <div class="text-sm text-gray-600">Drag and drop or click to upload multiple files</div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button
                        class="text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer"
                        onclick="prevStep(2)">Previous</button>
                    <button
                        class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer"
                        onclick="nextStep(4)">Next Step</button>
                </div>
            </div>
            <div class="step-content" style="display: <?php echo (($step == 4) ? 'block' : 'none') ?>" id="step4">
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
                                    <div class="font-medium" id="review-name">John Smith</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Date of Birth</div>
                                    <div class="font-medium" id="review-dob">15 March 1990</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Passport Number</div>
                                    <div class="font-medium" id="review-passport">A1234567</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Email</div>
                                    <div class="font-medium" id="review-email">john.smith@example.com</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="font-medium mb-4">Travel Details</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-500">Travel Dates</div>
                                    <div class="font-medium" id="review-dates">10 Apr 2025 - 24 Apr 2025</div>
                                </div>
                                <div>
                                    <div class="text-gray-500">Purpose</div>
                                    <div class="font-medium" id="review-purpose">Tourism</div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="font-medium mb-4">Documents</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-green-600">
                                    <i
                                        class="ri-checkbox-circle-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    Passport Copy
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i
                                        class="ri-checkbox-circle-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    Passport Photo
                                </div>
                                <div class="flex items-center text-green-600">
                                    <i
                                        class="ri-checkbox-circle-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                                    Supporting Documents (3 files)
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="font-medium mb-4">Payment Summary</h3>
                        <div class="space-y-2 text-sm mb-6">
                            <div class="flex justify-between">
                                <div>Visa Processing Fee</div>
                                <div>$160.00</div>
                            </div>
                            <div class="flex justify-between">
                                <div>Service Fee</div>
                                <div>$40.00</div>
                            </div>
                            <div class="flex justify-between font-medium pt-2 border-t">
                                <div>Total Amount</div>
                                <div>$200.00</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="border border-gray-300 rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="radio" name="payment" value="card" class="mr-3" checked>
                                    <div class="flex-1">
                                        <div class="font-medium">Credit/Debit Card</div>
                                        <div class="text-sm text-gray-500">Secure payment via Stripe</div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <i class="ri-visa-line text-blue-600 ri-lg"></i>
                                        <i class="ri-mastercard-line text-red-600 ri-lg"></i>
                                    </div>
                                </label>
                            </div>
                            <div class="border border-gray-300 rounded-lg p-4">
                                <label class="flex items-center">
                                    <input type="radio" name="payment" value="paypal" class="mr-3">
                                    <div class="flex-1">
                                        <div class="font-medium">PayPal</div>
                                        <div class="text-sm text-gray-500">Fast and secure payment</div>
                                    </div>
                                    <i class="ri-paypal-line text-blue-500 ri-lg"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start mt-6">
                        <input type="checkbox" class="mt-1 mr-3" required>
                        <div class="text-sm text-gray-600">
                            I confirm that all the information provided is accurate and I agree to the <a href="#"
                                class="text-primary hover:underline">Terms & Conditions</a> and <a href="#"
                                class="text-primary hover:underline">Privacy Policy</a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button
                        class="text-gray-600 font-medium px-8 py-3 border border-gray-300 !rounded-button hover:bg-gray-50 cursor-pointer"
                        onclick="prevStep(3)">Previous</button>
                    <button
                        class="bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer"
                        onclick="submitApplication()">Submit Application</button>
                </div>
            </div>
        </div>
    </div>
</main>
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-check-line text-green-500 ri-2x"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Application Submitted!</h3>
            <p class="text-gray-600 mb-6">Your visa application has been successfully submitted. You will receive a
                confirmation email shortly.</p>
            <div class="text-lg font-medium mb-6">Application ID: <span class="text-primary">VS-2025031501</span>
            </div>
            <a href="https://readdy.ai/home/da68aad3-a508-4219-bc9a-0f79e66cb4a4/46b5e02a-4f1b-4a1d-8fa9-204649e36205"
                data-readdy="true"
                class="block bg-primary text-white font-medium px-8 py-3 !rounded-button hover:bg-primary/90 cursor-pointer w-full text-center">Back
                to Home</a>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';
?>