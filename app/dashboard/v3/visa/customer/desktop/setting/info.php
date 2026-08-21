<div class="max-w-7xl mx-auto px-8 pt-12">
    <!-- Settings Content -->
    <div id="settings-content">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Account Settings</h1>
            <p class="text-gray-600">Manage your profile and account preferences</p>
        </div>
    </div>
</div>
<div class="max-w-7xl mx-auto px-8 pb-12">

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="flex space-x-8" id="settings-tabs">
            <button class="tab-btn text-primary border-b-2 border-primary py-2 px-1 font-medium" data-tab="profile">
                Profile Information
            </button>
            <button class="tab-btn text-gray-500 hover:text-gray-700 py-2 px-1 font-medium" data-tab="address">
                Address Information
            </button>
            <button class="tab-btn text-gray-500 hover:text-gray-700 py-2 px-1 font-medium" data-tab="account">
                Account Settings
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div id="tab-contents">
        <!-- Profile Information -->
        <div class="tab-content" id="tab-profile">
            <!-- Place your Profile Information HTML here -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profile Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" placeholder="John" id="firstName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" placeholder="Anderson" id="lastName"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" placeholder="john.anderson@email.com" id="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" placeholder="+91-9999999999" id="mobileNumber"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" placeholder="1990-05-15" id="dateOfBirth"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Number</label>
                        <input type="text" placeholder="A12345678" id="passportNumber"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                    </div>
                    <div>
                        <button id="saveProfileInformation"
                            class="bg-primary text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 !rounded-button whitespace-nowrap button-effect w-full sw:w-auto">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="tab-content hidden" id="tab-address">
            <!-- Place your Address Information HTML here -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Address Information</h2>
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address</label>
                            <input type="text" placeholder="A2/5A, WHS Block 2" id="address"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" placeholder="New Delhi" id="cityName"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                            <input type="text" placeholder="Delhi" id="stateName"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal
                                Code</label>
                            <input type="text" placeholder="110015" id="pinCode"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <input type="text" placeholder="India" id="countryName"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent text-sm">
                        </div>
                        <button id="saveAddressInformation"
                            class="bg-primary text-white px-8 py-3 rounded-lg font-medium hover:bg-blue-700 !rounded-button whitespace-nowrap button-effect w-full sw:w-auto">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-tab');

            // Toggle active tab button styles
            tabButtons.forEach(btn => {
                btn.classList.remove('text-primary', 'border-primary', 'border-b-2');
                btn.classList.add('text-gray-500');
            });
            button.classList.add('text-primary', 'border-primary', 'border-b-2');
            button.classList.remove('text-gray-500');

            // Show the correct tab content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-${target}`).classList.remove('hidden');
        });
    });
});
</script>