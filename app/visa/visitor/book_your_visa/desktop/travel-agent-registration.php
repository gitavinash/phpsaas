<?php

// Include header
include_once 'header.php';

?>

<main class="flex-grow pt-32 pb-20">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="relative h-40 bg-cover bg-center"
                style="background-image: url('https://readdy.ai/api/search-image?query=professional%20travel%20agency%20office%20with%20world%20map%20on%20wall%2C%20modern%20computers%2C%20travel%20brochures%20displayed%2C%20business%20environment%2C%20clean%20and%20organized%20workspace%2C%20soft%20natural%20lighting%2C%20travel%20industry%20setting&width=1200&height=300&seq=12&orientation=landscape')">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/80 to-primary/50"></div>
                <div class="absolute inset-0 flex items-center px-8">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Travel Agent Registration</h1>
                        <p class="text-white/90 max-w-xl">Join our network of professional travel agents and gain
                            access to exclusive deals, resources, and commission opportunities.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="flex items-center mb-6">
                    <div class="w-full bg-gray-200 h-1 rounded-full">
                        <div class="bg-primary h-1 rounded-full w-0" id="progressBar"></div>
                    </div>
                    <div class="ml-4 text-sm font-medium text-gray-500 whitespace-nowrap"><span
                            id="currentStep">1</span>/4</div>
                </div>

                <form id="agentRegistrationForm">
                    <!-- Step 1: Basic Information -->
                    <div class="form-step" id="step1">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3">
                                <i class="ri-building-line"></i>
                            </div>
                            Basic Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="companyName"
                                    class="block text-sm font-medium text-gray-700 mb-2">Company/Agency Name
                                    *</label>
                                <input type="text" id="companyName"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Enter your company name" required>
                            </div>

                            <div>
                                <label for="businessRegNumber"
                                    class="block text-sm font-medium text-gray-700 mb-2">Business Registration
                                    Number *</label>
                                <input type="text" id="businessRegNumber"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Enter registration number" required>
                            </div>

                            <div>
                                <label for="yearsOperation" class="block text-sm font-medium text-gray-700 mb-2">Years
                                    in Operation
                                    *</label>
                                <div class="relative">
                                    <select id="yearsOperation"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 appearance-none pr-10">
                                        <option value="">Select years</option>
                                        <option value="<1">Less than 1 year</option>
                                        <option value="1-3">1-3 years</option>
                                        <option value="4-7">4-7 years</option>
                                        <option value="8-15">8-15 years</option>
                                        <option value=">15">More than 15 years</option>
                                    </select>
                                    <i
                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 flex items-center justify-center"></i>
                                </div>
                            </div>

                            <div>
                                <label for="websiteUrl" class="block text-sm font-medium text-gray-700 mb-2">Website
                                    URL (optional)</label>
                                <input type="url" id="websiteUrl"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="https://yourcompany.com">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="nextToStep2"
                                class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Continue
                                to Contact Details</button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Details -->
                    <div class="form-step hidden" id="step2">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3">
                                <i class="ri-contacts-line"></i>
                            </div>
                            Contact Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="contactName" class="block text-sm font-medium text-gray-700 mb-2">Primary
                                    Contact Name
                                    *</label>
                                <input type="text" id="contactName"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Full name" required>
                            </div>

                            <div>
                                <label for="contactEmail" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address *</label>
                                <input type="email" id="contactEmail"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="email@example.com" required>
                            </div>

                            <div>
                                <label for="contactPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number *</label>
                                <input type="tel" id="contactPhone"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="+1 (555) 123-4567" required>
                            </div>

                            <div>
                                <label for="businessAddress"
                                    class="block text-sm font-medium text-gray-700 mb-2">Business Address *</label>
                                <input type="text" id="businessAddress"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Street address" required>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City
                                    *</label>
                                <input type="text" id="city"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="City" required>
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country
                                    *</label>
                                <div class="relative">
                                    <select id="country"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 appearance-none pr-10">
                                        <option value="">Select country</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="IN">India</option>
                                        <option value="SG">Singapore</option>
                                        <option value="AE">United Arab Emirates</option>
                                    </select>
                                    <i
                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 flex items-center justify-center"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" id="backToStep1"
                                class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Back</button>
                            <button type="button" id="nextToStep3"
                                class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Continue
                                to Business Credentials</button>
                        </div>
                    </div>

                    <!-- Step 3: Business Credentials -->
                    <div class="form-step hidden" id="step3">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3">
                                <i class="ri-verified-badge-line"></i>
                            </div>
                            Business Credentials
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="iataNumber" class="block text-sm font-medium text-gray-700 mb-2">IATA
                                    Number (if applicable)</label>
                                <input type="text" id="iataNumber"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Enter IATA number">
                            </div>

                            <div>
                                <label for="associations" class="block text-sm font-medium text-gray-700 mb-2">Travel
                                    Association
                                    Memberships</label>
                                <div class="relative">
                                    <select id="associations"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 appearance-none pr-10"
                                        multiple>
                                        <option value="ASTA">ASTA</option>
                                        <option value="ABTA">ABTA</option>
                                        <option value="USTOA">USTOA</option>
                                        <option value="PATA">PATA</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <i
                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 flex items-center justify-center"></i>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            <div>
                                <label for="businessLicense"
                                    class="block text-sm font-medium text-gray-700 mb-2">Business License Upload
                                    *</label>
                                <div class="relative">
                                    <input type="file" id="businessLicense" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                        required>
                                    <label for="businessLicense"
                                        class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-button text-gray-500 cursor-pointer hover:bg-gray-50">
                                        <span class="file-input-label" id="businessLicenseLabel">Upload license
                                            (PDF, JPG, PNG)</span>
                                        <i
                                            class="ri-upload-2-line text-primary w-5 h-5 flex items-center justify-center"></i>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 5MB</p>
                            </div>

                            <div>
                                <label for="companyLogo" class="block text-sm font-medium text-gray-700 mb-2">Company
                                    Logo Upload</label>
                                <div class="relative">
                                    <input type="file" id="companyLogo" class="hidden" accept=".jpg,.jpeg,.png">
                                    <label for="companyLogo"
                                        class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-button text-gray-500 cursor-pointer hover:bg-gray-50">
                                        <span class="file-input-label" id="companyLogoLabel">Upload logo (JPG,
                                            PNG)</span>
                                        <i
                                            class="ri-upload-2-line text-primary w-5 h-5 flex items-center justify-center"></i>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Recommended size: 300x300px</p>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" id="backToStep2"
                                class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Back</button>
                            <button type="button" id="nextToStep4"
                                class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Continue
                                to Account Security</button>
                        </div>
                    </div>

                    <!-- Step 4: Account Security -->
                    <div class="form-step hidden" id="step4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mr-3">
                                <i class="ri-shield-keyhole-line"></i>
                            </div>
                            Account Security
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Create
                                    Password *</label>
                                <div class="relative">
                                    <input type="password" id="password"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 pr-10"
                                        placeholder="Create a strong password" required>
                                    <button type="button" id="togglePassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-5 h-5 flex items-center justify-center">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                                <div class="password-strength-meter mt-2">
                                    <div id="passwordStrengthBar"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" id="passwordStrengthText">Password strength
                                </p>
                            </div>

                            <div>
                                <label for="confirmPassword"
                                    class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" id="confirmPassword"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 pr-10"
                                        placeholder="Confirm your password" required>
                                    <button type="button" id="toggleConfirmPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-5 h-5 flex items-center justify-center">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" id="passwordMatchText"></p>
                            </div>

                            <div>
                                <label for="securityQuestion"
                                    class="block text-sm font-medium text-gray-700 mb-2">Security Question *</label>
                                <div class="relative">
                                    <select id="securityQuestion"
                                        class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700 appearance-none pr-10"
                                        required>
                                        <option value="">Select a security question</option>
                                        <option value="firstPet">What was the name of your first pet?</option>
                                        <option value="motherMaiden">What is your mother's maiden name?</option>
                                        <option value="birthCity">In which city were you born?</option>
                                        <option value="schoolName">What was the name of your first school?</option>
                                        <option value="favoriteTeacher">Who was your favorite teacher?</option>
                                    </select>
                                    <i
                                        class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 w-4 h-4 flex items-center justify-center"></i>
                                </div>
                            </div>

                            <div>
                                <label for="securityAnswer"
                                    class="block text-sm font-medium text-gray-700 mb-2">Security Answer *</label>
                                <input type="text" id="securityAnswer"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Your answer" required>
                            </div>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-start mb-4">
                                <div class="relative flex items-center h-5">
                                    <input type="checkbox" id="termsConditions"
                                        class="custom-checkbox opacity-0 absolute h-5 w-5" required>
                                    <label for="termsConditions"
                                        class="custom-checkbox-label flex items-center text-sm text-gray-700 relative">
                                        I agree to the <a href="#" class="text-primary hover:underline ml-1 mr-1">Terms
                                            & Conditions</a>
                                        and <a href="#" class="text-primary hover:underline mr-1">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="relative flex items-center h-5">
                                    <input type="checkbox" id="marketingConsent"
                                        class="custom-checkbox opacity-0 absolute h-5 w-5">
                                    <label for="marketingConsent"
                                        class="custom-checkbox-label flex items-center text-sm text-gray-700 relative">
                                        I would like to receive marketing communications about products, services,
                                        and promotions
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i
                                        class="ri-information-line text-primary w-5 h-5 flex items-center justify-center"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-700">
                                        Your application will be reviewed by our team within 1-2 business days.
                                        You'll receive a confirmation email once your account is approved.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" id="backToStep3"
                                class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Back</button>
                            <div class="flex space-x-4">
                                <button type="button" id="clearForm"
                                    class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Clear
                                    Form</button>
                                <button type="submit" id="submitForm"
                                    class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap">Complete
                                    Registration</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Benefits of Becoming a Partner Agent</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i class="ri-percent-line text-primary ri-lg"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-2">Competitive Commissions</h3>
                        <p class="text-gray-600 text-sm">Earn up to 15% commission on visa services and additional
                            travel products.</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i class="ri-tools-line text-primary ri-lg"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-2">Dedicated Tools</h3>
                        <p class="text-gray-600 text-sm">Access our agent portal with real-time tracking, reporting,
                            and marketing resources.</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                            <i class="ri-customer-service-2-line text-primary ri-lg"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-2">Priority Support</h3>
                        <p class="text-gray-600 text-sm">Get dedicated account manager and priority processing for
                            your clients' applications.</p>
                    </div>
                </div>
                <!-- 
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h3 class="font-medium text-gray-800 mb-4">Frequently Asked Questions</h3>

                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                class="faq-toggle w-full flex justify-between items-center p-4 text-left font-medium text-gray-700 hover:bg-gray-50">
                                <span>How long does the approval process take?</span>
                                <i class="ri-arrow-down-s-line w-5 h-5 flex items-center justify-center"></i>
                            </button>
                            <div class="faq-content hidden p-4 pt-0 text-sm text-gray-600 border-t border-gray-200">
                                The approval process typically takes 1-2 business days. We'll review your
                                application and credentials, and you'll receive an email notification once your
                                account is approved.
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                class="faq-toggle w-full flex justify-between items-center p-4 text-left font-medium text-gray-700 hover:bg-gray-50">
                                <span>What documents are required for registration?</span>
                                <i class="ri-arrow-down-s-line w-5 h-5 flex items-center justify-center"></i>
                            </button>
                            <div class="faq-content hidden p-4 pt-0 text-sm text-gray-600 border-t border-gray-200">
                                You'll need to provide your business registration certificate, proof of address, and
                                IATA number (if applicable). Additional documents may be requested during the
                                verification process.
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button
                                class="faq-toggle w-full flex justify-between items-center p-4 text-left font-medium text-gray-700 hover:bg-gray-50">
                                <span>How are commissions paid out?</span>
                                <i class="ri-arrow-down-s-line w-5 h-5 flex items-center justify-center"></i>
                            </button>
                            <div class="faq-content hidden p-4 pt-0 text-sm text-gray-600 border-t border-gray-200">
                                Commissions are calculated monthly and paid via bank transfer or PayPal. You can
                                track your earnings in real-time through our agent portal and request payouts once
                                you reach the minimum threshold.
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</main>
<?php
// Include footer
include_once 'footer.php';
?>