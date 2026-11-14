<?php
include_once 'header.php';
?>

<section class="min-h-[65vh] flex items-center justify-center py-20 px-4 bg-[#FAF7F5]">
    <div class="max-w-lg w-full mx-auto text-center bg-white border border-[#E8DDD5] rounded-2xl p-8 sm:p-12 shadow-sm">
        <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] flex items-center justify-center text-[#C47C68]">
            <i class="ri-error-warning-line text-2xl" aria-hidden="true"></i>
        </div>
        <span class="vl-eyebrow">500 &bull; Atelier System Notice</span>
        <h1 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-1 mb-3">Arrangement Under Crafting</h1>
        <p class="text-sm text-[#6E675F] leading-relaxed mb-8">
            Our atelier system is temporarily refreshing. Our technical florists are tending to this immediately.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
            <a href="/" class="vl-btn-primary">
                <span>Return to Homepage</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
            <a href="/contact-us/" class="vl-btn-outline">
                <span>Contact Concierge</span>
            </a>
        </div>

        <div class="pt-6 border-t border-[#E8DDD5] text-xs text-[#6E675F]">
            <span>Need urgent assistance with an order? Call </span>
            <a href="tel:+919910744119" class="font-medium text-[#242220] hover:text-[#C47C68] transition-colors">+91 99107 44119</a>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
