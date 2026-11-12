<?php
$payment_error_reason = '';
$transaction_id = '';
$rawOrderId = isset($_GET['id']) ? (string) $_GET['id'] : '';
$sanitizedRetryId = preg_replace('/[^A-Za-z0-9+\/=_-]/', '', $rawOrderId);

require_once 'header.php';
$retryUrl = $sanitizedRetryId !== '' ? '/shipping/?id=' . safe_html($sanitizedRetryId) : '/checkout/';
?>

<section class="bg-[#FAF7F2] py-10 sm:py-16 md:py-24 px-4">
    <div class="max-w-2xl mx-auto bg-white border border-[#E6DDD3] shadow-sm rounded-2xl p-6 sm:p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-[#FEF2F2] text-[#991B1B] flex items-center justify-center">
            <i class="ri-close-line text-3xl" aria-hidden="true"></i>
        </div>

        <span class="tbe-eyebrow">Payment Status</span>
        <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 mb-3">Payment Could Not Be Completed</h1>
        <p class="text-[#5E5650] text-sm sm:text-base leading-relaxed mb-8">
            Your card or UPI authorization was not completed. No charges have been finalized—you can safely retry your payment or reach out to our concierge team.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-3 mb-8">
            <a href="<?php echo $retryUrl; ?>" class="tbe-btn-forest justify-center">
                <i class="ri-refresh-line" aria-hidden="true"></i>
                <span>Retry Payment</span>
            </a>
            <a href="/" class="tbe-btn-outline justify-center">
                Return to Homepage
            </a>
        </div>

        <div class="pt-6 border-t border-[#E6DDD3] flex flex-wrap justify-center items-center gap-4 text-xs text-[#5E5650]">
            <span>Need assistance?</span>
            <a href="tel:+919910744119" class="text-[#183028] font-medium hover:text-[#9B5637] transition-colors">
                Call +91-9910744119
            </a>
            <span aria-hidden="true">&middot;</span>
            <a href="/contact-us/" class="text-[#9B5637] font-medium underline underline-offset-4 hover:text-[#183028] transition-colors">
                Contact Concierge
            </a>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>
