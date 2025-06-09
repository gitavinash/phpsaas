<?php

require_once 'header.php';

?>
<main class="pt-20">
    <!-- Hero Section -->
    <section class="relative min-h-[300px] flex items-center bg-cover bg-center">
        <div class=" absolute inset-0 bg-gradient-to-r from-primary/90 to-primary/50">
        </div>
        <div class="container mx-auto px-4 relative">
            <div class="max-w-3xl text-white">
                <h1 class="text-5xl font-bold mb-6"><?php echo $page_title ?></h1>
            </div>
        </div>
    </section>
    <!-- Our Story Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="pageDescription">
                <?php echo $page_description ?>
            </div>
        </div>
    </section>
</main>
<?php

require_once 'footer.php'

?>