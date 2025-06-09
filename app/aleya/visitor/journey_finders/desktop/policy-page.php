<?php

require_once 'header.php';

?>
<!-- Hero Section -->
<section class="bg-gray-800 py-12">
    <div class="container mx-auto px-4">
        <div class="mx-auto">
            <div class="flex items-center text-gray-300 text-sm mb-4">
                <a href="/" data-readdy="true" class="hover:text-white transition">Home</a>
                <span class="mx-2">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
                <span class="text-white"><?php echo $page_title ?></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                <?php echo $page_title ?>
            </h1>
        </div>
    </div>
</section>
<!-- Main Content -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="bg-white rounded-lg shadow-sm p-8">
                <!-- Introduction -->
                <section id="introduction" class="mb-12 editorPageDescription">
                    <?php echo $page_description ?>
                </section>
            </div>
        </div>
    </div>
</section>
<?php

require_once 'footer.php';

?>