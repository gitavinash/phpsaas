<?php

require_once 'header.php';
require_once UTIL_FUNCTIONS;

if ($_GET['id']) {

    $transaction_id = base64_decode($id);

    if ($checkTransaction = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "transaction WHERE transaction_order_id='{$transaction_id}' AND transaction_site_id=" . SITE_ID . " ORDER BY transaction_status ASC")) {
        if (mysqli_num_rows($checkTransaction) != 0) {
            extract(mysqli_fetch_assoc($checkTransaction));
            if ($transaction_status == 2) {
?>
                <main class="flex-grow pt-32 pb-20">
                    <div class="container mx-auto px-4 max-w-4xl">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <!-- Banner -->
                            <div class="relative h-48 bg-cover bg-center">
                                <div class="absolute inset-0 bg-gradient-to-r from-red-700/80 to-red-500/50"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <h1 class="text-3xl md:text-3xl font-bold text-white text-center">
                                        Payment Failed
                                    </h1>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-8">
                                <div class="max-w-2xl mx-auto">
                                    <!-- Icon and message -->
                                    <div class="text-center mb-12">
                                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <i class="ri-close-line text-red-600 ri-3x"></i>
                                        </div>
                                        <p class="text-gray-700 text-lg mb-4">
                                            Unfortunately, your payment could not be processed for
                                            <?php echo SITE_NAME ?>.<br />
                                            Please try again or contact our support team for assistance.
                                        </p>
                                    </div>

                                    <!-- Transaction Details -->
                                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                                        <h3 class="font-semibold text-gray-800 mb-4">Transaction Details</h3>
                                        <div class="space-y-3">
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Transaction Id</span>
                                                <span class="font-medium text-gray-800">
                                                    <?php echo getInitials(SITE_NAME) . '-00' . $transaction_order_id ?>
                                                </span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Date</span>
                                                <span class="font-medium text-gray-800">
                                                    <?php echo date('F j, Y h:i A', strtotime($transaction_time)); ?>
                                                </span>
                                            </div>
                                            <div class="flex justify-between py-2 border-b border-gray-200">
                                                <span class="text-gray-600">Status</span>
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                    <i class="ri-error-warning-line mr-1 w-4 h-4 flex items-center justify-center"></i>
                                                    Payment Failed
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Next Steps -->
                                    <div class="bg-red-50 rounded-xl p-6 mb-8">
                                        <h3 class="font-semibold text-gray-800 mb-4">Next Steps</h3>
                                        <div class="space-y-4">
                                            <div class="flex items-start">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-1">
                                                    <i class="ri-refresh-line text-red-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="font-medium text-gray-800 mb-1">Retry Payment</h4>
                                                    <p class="text-gray-600">You can try making the payment again using a different
                                                        payment method.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-1">
                                                    <i class="ri-customer-service-2-line text-red-600"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="font-medium text-gray-800 mb-1">Contact Support</h4>
                                                    <p class="text-gray-600">If the issue persists, please reach out to our customer
                                                        support team.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="text-center space-y-4">
                                        <a href="<?php echo  '/payment/generate/?id=' . base64_encode($transaction_id) . '&pg_type=order&orderType=visa_application&app=default&pgRequestType=default'; ?>"
                                            class="inline-flex items-center justify-center bg-red-600 text-white hover:bg-red-700 font-medium !rounded-button px-8 py-3 cursor-pointer whitespace-nowrap">
                                            <i class="ri-refresh-line mr-2 w-5 h-5 flex items-center justify-center"></i>
                                            Retry Payment
                                        </a>
                                        <p class="text-gray-600">
                                            Need help? <a href="/contact-us/" class="text-red-600 hover:underline">Contact our support
                                                team</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
<?php
            }
        }
    }
}

require_once 'footer.php';
