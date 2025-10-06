<?php include_once 'header.php'; ?>

<!-- Header End -->
<section class="relative bg-white/95 backdrop-blur-sm shadow-sm py-8">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        <div class="mb-6">
            <nav class="text-sm" aria-label="breadcrumb">
                <ol class="flex space-x-2 text-gray-600">
                    <li>
                        <a href="#" class="hover:text-blue-600 transition">Home</a>
                    </li>
                    <li class="before:content-['/'] before:mx-2 before:text-gray-400 text-gray-700">
                        <?php echo $page_title ?>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Title + Content -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-serif text-gray-900 mb-4">
                <?php echo $page_title ?>
            </h2>
            <div class="prose max-w-none text-gray-700 sameFont">
                <?php echo $page_description; ?>
            </div>
        </div>

    </div>
</section>

<?php include_once 'footer.php'; ?>