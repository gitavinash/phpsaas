<?php

include_once 'header.php';

?>

<section class="bg-[#FAF7F2] py-8 sm:py-12 md:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-xs sm:text-sm mb-4 sm:mb-6 text-[#5E5650]" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="text-[#9B5637] hover:text-[#183028] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#1C1A18] font-medium" aria-current="page"><?php echo safe_html((string) $post_title); ?></li>
            </ol>
        </nav>

        <!-- Editorial Journal Post -->
        <article class="bg-white border border-[#E6DDD3] shadow-sm rounded-2xl p-5 sm:p-10 md:p-12">
            <header class="pb-4 sm:pb-6 mb-5 sm:mb-8 border-b border-[#E6DDD3]">
                <span class="tbe-eyebrow">Atelier Journal</span>
                <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl text-[#1C1A18] mt-1 leading-tight">
                    <?php echo safe_html((string) $post_title); ?>
                </h1>
            </header>

            <div class="prose max-w-none text-[#1C1A18] text-sm sm:text-base leading-relaxed sameFont">
                <?php echo $post_description; ?>
            </div>
        </article>
    </div>
</section>

<?php

include_once 'footer.php';

?>
