<?php
require_once 'header.php';
$safeMediaTitle = safe_html((string) ($media_title ?? 'Atelier Visual'));
$safeMediaUrl = safe_html((string) ($media_url ?? ''));
?>

<section class="bg-[#FAF7F2] py-8 sm:py-12 md:py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-xs sm:text-sm mb-4 sm:mb-6 text-[#5E5650]" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="text-[#9B5637] hover:text-[#183028] transition-colors">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-[#1C1A18] font-medium truncate" aria-current="page"><?php echo $safeMediaTitle; ?></li>
            </ol>
        </nav>

        <article class="bg-white border border-[#E6DDD3] rounded-2xl shadow-sm overflow-hidden">
            <?php if ($safeMediaTitle !== ''): ?>
                <header class="px-4 sm:px-8 py-4 sm:py-5 border-b border-[#E6DDD3] bg-[#FAF7F2]">
                    <span class="tbe-eyebrow">Studio Media</span>
                    <h1 class="font-serif text-xl sm:text-3xl text-[#1C1A18] mt-1"><?php echo $safeMediaTitle; ?></h1>
                </header>
            <?php endif; ?>
            <div class="p-4 sm:p-8 flex justify-center bg-white">
                <?php if ($safeMediaUrl !== ''): ?>
                    <img src="<?php echo SITE_URL . $safeMediaUrl; ?>"
                        alt="<?php echo $safeMediaTitle; ?>"
                        class="max-w-full max-h-[75vh] w-auto h-auto rounded-xl object-contain border border-[#E6DDD3]">
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>

<?php
require_once 'footer.php';
?>
