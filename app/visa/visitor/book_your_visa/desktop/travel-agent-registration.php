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
                                    placeholder="Enter your company name" name="company_name" required>
                            </div>

                            <div>
                                <label for="gstNumber" class="block text-sm font-medium text-gray-700 mb-2">GST
                                    Number *</label>
                                <input type="text" id="gstNumber"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Enter registration number" name="gst_no" required>
                            </div>

                            <div>
                                <label for="yearsOperation" class="block text-sm font-medium text-gray-700 mb-2">Years
                                    in Operation
                                    *</label>
                                <div class="relative">
                                    <select id="yearsOperation" name="years_operation"
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
                                <input type="url" id="websiteUrl" name="website_url"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="https://yourcompany.com">
                            </div>
                        </div>

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
                                    placeholder="Full name" name="full_name" required>
                            </div>

                            <div>
                                <label for="contactEmail" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address *</label>
                                <input type="email" id="contactEmail"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="email@example.com" name="email" required>
                            </div>

                            <div>
                                <label for="contactPhone" class="block text-sm font-medium text-gray-700 mb-2">Mobile
                                    Number *</label>
                                <input type="tel" id="contactPhone"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="+1 (555) 123-4567" name="mobile" required>
                            </div>

                            <div>
                                <label for="businessAddress"
                                    class="block text-sm font-medium text-gray-700 mb-2">Business Address *</label>
                                <input type="text" id="businessAddress"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="Street address" name="address" required>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City
                                    *</label>
                                <input type="text" id="city"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="City" name="city" required>
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">State
                                    *</label>
                                <input type="text" id="state"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-button text-gray-700"
                                    placeholder="City" name="state" required>
                            </div>

                        </div>

                        <div class="flex justify-end">
                            <input type="hidden" name="subject" value="New travel agent application">
                            <input type="hidden" name="toAddress"
                                value="<?php echo base64_encode("avineshpaliwal33@gmail.com") ?>">
                            <input type="hidden" name="redirectURL" value="/travel-agent-thank-you/">
                            <button type="submit" id="travelAgentRegistrationBtn"
                                class="bg-primary text-white hover:bg-primary/90 font-medium !rounded-button px-6 py-3 cursor-pointer whitespace-nowrap"
                                value="Submit Application">Submit Application</button>
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