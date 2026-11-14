<?php
include_once 'header.php';
?>

<!-- Editorial Atelier Hero -->
<section class="relative bg-[#FAF7F5] py-16 md:py-24 border-b border-[#E8DDD5]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-6 text-[#6E675F] flex justify-center" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li><a href="/" class="text-[#C47C68] hover:text-[#242220] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#242220] font-medium" aria-current="page">About Atelier</li>
            </ol>
        </nav>
        <span class="vl-eyebrow">Our Botanical Philosophy</span>
        <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-[#242220] mt-2 mb-6 font-normal tracking-tight">
            Curating Poetic Elegance &amp; Living Art
        </h1>
        <p class="text-base sm:text-lg text-[#6E675F] max-w-2xl mx-auto leading-relaxed">
            Founded in 2018 across Chhatarpur and Gurugram, Veloura is a contemporary floral atelier dedicated to the delicate balance of architectural form, botanical romance, and sustainable luxury.
        </p>
    </div>
</section>

<!-- Story & Craftsmanship Split Section -->
<section class="py-16 md:py-24 bg-white border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-2xl overflow-hidden shadow-lg border border-[#E8DDD5] aspect-[4/5] max-w-md mx-auto">
                    <img src="<?php echo htmlspecialchars(USER_IMAGES . 'andblooms-store.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Veloura Floral Atelier Studio"
                        class="w-full h-full object-cover">
                </div>
                <div class="hidden sm:block absolute -bottom-6 -right-4 bg-[#FAF7F5] border border-[#E8DDD5] rounded-xl p-5 shadow-md max-w-xs">
                    <span class="vl-eyebrow">Handcrafted Daily</span>
                    <p class="font-serif text-base text-[#242220] leading-snug">
                        Every bouquet is tied with pure silk ribbons and freshly conditioned stems.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-6">
                <span class="vl-eyebrow">The Atelier Story</span>
                <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] leading-tight font-normal">
                    Transforming Nature's Fleeting Moments into Timeless Memories
                </h2>
                <p class="text-sm sm:text-base text-[#6E675F] leading-relaxed">
                    What began as an intimate design studio in South Delhi has grown into one of the capital's premier luxury floral houses. At Veloura, we perceive flowers not merely as gifts, but as poetic messengers of affection, celebration, and gratitude.
                </p>
                <p class="text-sm sm:text-base text-[#6E675F] leading-relaxed">
                    Our master florists combine classical European botanical arrangements—inspired by Italian floristry and Parisian styling—with rich botanical varieties cultivated in sustainable hill farms across India and imported direct from Dutch auctions.
                </p>
                <div class="pt-4 grid grid-cols-2 gap-4">
                    <div class="border-l-2 border-[#C47C68] pl-4">
                        <span class="block font-serif text-2xl sm:text-3xl text-[#242220] font-normal">240+</span>
                        <span class="text-xs uppercase tracking-wider text-[#6E675F]">Stems Harvested Weekly</span>
                    </div>
                    <div class="border-l-2 border-[#3D5447] pl-4">
                        <span class="block font-serif text-2xl sm:text-3xl text-[#242220] font-normal">100%</span>
                        <span class="text-xs uppercase tracking-wider text-[#6E675F]">Plastic-Free Packaging</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values & Pillars -->
<section class="py-16 md:py-24 bg-[#FAF7F5] border-b border-[#E8DDD5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="vl-eyebrow">Our Guiding Pillars</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-2 mb-4 font-normal">
                Devoted to Thoughtful Floristry
            </h2>
            <p class="text-sm sm:text-base text-[#6E675F] leading-relaxed">
                The exacting craftsmanship, ethical standards, and sensory devotion behind every single commission.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] text-[#C47C68] flex items-center justify-center mb-6 text-2xl">
                    <i class="ri-leaf-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-3">Conscious Sourcing</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    We partner exclusively with sustainable farms that practice water stewardship and ethical harvesting, ensuring pristine blooms that honor the earth.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] text-[#3D5447] flex items-center justify-center mb-6 text-2xl">
                    <i class="ri-palette-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-3">Editorial Artistry</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    Rejecting generic arrangements, our creations reflect dynamic balance, asymmetry, tonal subtlety, and organic movement inspired by fine-art paintings.
                </p>
            </div>

            <div class="bg-white border border-[#E8DDD5] rounded-2xl p-8 shadow-sm text-center flex flex-col items-center">
                <div class="w-14 h-14 rounded-full bg-[#FAF7F5] border border-[#E8DDD5] text-[#C47C68] flex items-center justify-center mb-6 text-2xl">
                    <i class="ri-truck-line" aria-hidden="true"></i>
                </div>
                <h3 class="font-serif text-xl text-[#242220] mb-3">White-Glove Delivery</h3>
                <p class="text-sm text-[#6E675F] leading-relaxed">
                    Climate-controlled concierge transport safeguards delicate petals across Delhi NCR, ensuring arrangements arrive in flawless bloom with handwritten cards.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Atelier Leadership -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="vl-eyebrow">The Atelier Leadership</span>
            <h2 class="font-serif text-3xl sm:text-4xl text-[#242220] mt-2 mb-4 font-normal">
                Curators Behind the Craft
            </h2>
            <p class="text-sm sm:text-base text-[#6E675F]">
                Guided by visionary floral directors passionate about contemporary luxury.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl overflow-hidden shadow-sm group">
                <div class="aspect-[4/4] overflow-hidden bg-white">
                    <img src="<?php echo htmlspecialchars(USER_IMAGES . 'shalini_dugar.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Shalini Dugar - Founder & Creative Director"
                        class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="p-6 sm:p-8 text-center">
                    <h3 class="font-serif text-2xl text-[#242220] mb-1">Shalini Dugar</h3>
                    <p class="text-xs uppercase tracking-[0.14em] text-[#C47C68] font-semibold mb-3">Founder &amp; Creative Director</p>
                    <p class="text-sm text-[#6E675F] leading-relaxed">
                        With an eye refined by international fashion and botanical design, Shalini guides the seasonal moodboards and artistic direction of every Veloura collection.
                    </p>
                </div>
            </div>

            <div class="bg-[#FAF7F5] border border-[#E8DDD5] rounded-2xl overflow-hidden shadow-sm group">
                <div class="aspect-[4/4] overflow-hidden bg-white">
                    <img src="<?php echo htmlspecialchars(USER_IMAGES . 'ayush_dugar.jpeg', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Ayush Dugar - Managing Director"
                        class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="p-6 sm:p-8 text-center">
                    <h3 class="font-serif text-2xl text-[#242220] mb-1">Ayush Dugar</h3>
                    <p class="text-xs uppercase tracking-[0.14em] text-[#3D5447] font-semibold mb-3">Managing Director</p>
                    <p class="text-sm text-[#6E675F] leading-relaxed">
                        Directing global sourcing logistics, bespoke retail partnerships, and seamless client experiences that redefine modern luxury gifting across India.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include_once 'footer.php';
?>
