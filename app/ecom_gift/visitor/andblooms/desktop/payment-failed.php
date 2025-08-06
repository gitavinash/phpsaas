<?php
// Dynamic variables (replace with actual values from backend)
$payment_error_reason = '';
$transaction_id = '';

require_once 'header.php';
?>

<!-- Full Page Themed Payment Declined -->
<section class="min-h-screen bg-red-100 flex flex-col justify-center items-center text-center py-12">

    <!-- Banner -->
    <div class="w-full bg-primary-600 text-white py-10">
        <h1 class="text-3xl text-red-600 font-bold">Payment Declined</h1>
        <p class="text-base text-gray-700 mt-2">Your payment could not be completed</p>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-8 border border-gray-200">

        <!-- Error Icon -->
        <div class="mb-6">
            <i class="ri-close-line w-20 h-20 text-8xl mx-auto text-red-500"></i>
        </div>

        <!-- Error Message -->
        <h2 class="text-2xl font-bold text-red-600 mb-2">We couldn't process your payment</h2>
        <p class="text-gray-700 mb-6">Please review the details below and try again.</p>

        <!-- Error Details -->
        <div class="bg-red-100 border border-red-200 rounded p-4 text-left mb-6 text-sm ">
            <p class="text-gray-700"><strong class="text-red-600">Reason:</strong> <?php echo $payment_error_reason; ?>
            </p>
            <p class="text-gray-700" class="mt-2 text-red-600"><strong class="text-red-600">Transaction ID:</strong>
                <?php echo $transaction_id; ?></p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
            <a href="/checkout/"
                class="bg-primary text-white px-6 py-3 rounded hover:bg-primary-700 transition text-sm">
                Retry Payment
            </a>
            <a href="/" class="text-blue-600 hover:underline text-sm flex items-center justify-center">
                Back to Homepage
            </a>
        </div>

        <!-- Support -->
        <p class="text-xs text-gray-500">
            Still need help? <a href="/contact-us/" class="underline hover:text-blue-600">Contact Support</a>
        </p>
    </div>

</section>

<?php require_once 'footer.php'; ?>