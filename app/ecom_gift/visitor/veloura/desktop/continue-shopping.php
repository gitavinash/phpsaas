<?php
require_once 'header.php';
?>

<section class="min-h-[60vh] flex items-center justify-center py-20 px-4 bg-[#FAF7F5]">
    <div class="max-w-md w-full mx-auto text-center bg-white border border-[#E8DDD5] rounded-2xl p-8 sm:p-12 shadow-sm">
        <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] flex items-center justify-center text-[#C47C68]">
            <i class="ri-shopping-bag-3-line text-2xl" aria-hidden="true"></i>
        </div>
        <span class="vl-eyebrow">Atelier Floral Bag</span>
        <h1 class="font-serif text-2xl sm:text-3xl text-[#242220] mt-1 mb-3">Your Bag is Empty</h1>
        <p class="text-sm text-[#6E675F] leading-relaxed mb-8">
            Your floral bag holds no blooms at the moment. Explore our seasonal collections, fresh arrangements, and botanical masterclasses.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/shop/" class="vl-btn-primary">
                <span>Explore Creations</span>
                <i class="ri-arrow-right-line" aria-hidden="true"></i>
            </a>
            <a href="/events/" class="vl-btn-outline">
                <span>View Workshops</span>
            </a>
        </div>
    </div>
</section>

<?php
require_once 'footer.php';
?>
