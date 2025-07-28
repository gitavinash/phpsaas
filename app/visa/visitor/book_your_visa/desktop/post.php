<?php

require_once 'header.php';

?>
<main class="pt-28 pb-16">
    <div class="container mx-auto px-4 max-w-6xl">
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4 px-4" aria-label="Breadcrumb">
            <a href="/" class="hover:underline text-primary font-medium">Home</a>
            <span>/</span>
            <a href="/blogs/" class="hover:underline text-primary font-medium">Blogs</a>
            <span>/</span>
            <span class="text-gray-500"><?php echo $post_title ?></span>
        </nav>
        <div class="flex flex-col lg:flex-row gap-12">
            <article class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="relative">
                        <img src="<?php echo $post_image ?>" alt="<?php echo $post_title ?>"
                            class="w-full h-[400px] object-cover">
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-6">
                            <?php echo (($link_is_featured == 1) ? '<span
                                class="px-3 py-1 text-sm bg-primary text-white rounded-full">Featured</span>' : '') ?>
                            <span class="px-3 py-1 text-sm bg-blue-50 text-blue-600 rounded-full">
                                <?php echo $category_title ?></span>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-6 leading-tight"><?php echo $post_title ?></h1>
                        <!-- <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-4">
                                <img src="<?php echo $profile_image ?>" alt="Victoria Chen"
                                    class="w-12 h-12 rounded-full object-cover">
                                <div>
                                    <div class="font-semibold text-gray-900"><?php echo $profile_display_name ?></div>
                                    <div class="text-sm text-gray-500">Published on
                                        <?php echo date('d M Y h:i:A', strtotime($link_published)) ?></div>
                                </div>
                            </div>
                        </div> -->
                        <div class="prose prose-lg max-w-none" id="articleContent">
                            <?php echo $post_description ?>
                        </div>
                    </div>
            </article>
            <aside class="lg:w-1/3">
                <div class="sticky top-32 space-y-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex items-center gap-4 mb-6">
                            <img src="<?php echo $profile_image ?>" alt="<?php echo $profile_display_name ?>"
                                class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <h3 class="font-semibold text-gray-900"><?php echo $profile_display_name ?></h3>
                                <p class="text-sm text-gray-500">Travel Expert & Writer</p>
                            </div>
                        </div>
                        <a href="tel:+917291874705" class="block w-full">
                            <button
                                class="w-full bg-primary text-white font-medium py-2 !rounded-button hover:bg-primary/90">
                                Call Now for Visa Assistance
                            </button>
                        </a>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="mb-6">
                            <div class="relative">
                                <form action="/search/" method="get">
                                    <input type="text" name="q" placeholder="Search articles..."
                                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-button focus:outline-none focus:border-primary">
                                    <i
                                        class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </form>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-semibold mb-4">Related Articles</h3>
                            <div class="space-y-4">
                                <!-- <a href="#" class="group block">
                                    <div class="flex items-start gap-3">
                                        <img src="https://readdy.ai/api/search-image?query=modern%20airport%20terminal%20interior%20with%20travelers%2C%20professional%20photography&width=80&height=80&seq=related4&orientation=squarish"
                                            alt="Airport Guide" class="w-16 h-16 object-cover rounded-lg">
                                        <div>
                                            <h4
                                                class="text-sm font-medium text-gray-900 group-hover:text-primary transition-colors">
                                                Complete Airport Navigation Guide 2025</h4>
                                            <p class="text-xs text-gray-500 mt-1">5 min read</p>
                                        </div>
                                    </div>
                                </a> -->
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold mb-4">Popular Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php

                            if ($tagName != '') {
                                $tagName = explode(', ', $tagName);

                                for ($i = 0; $i < sizeof($tagName); $i++) {
                                    echo "<a href='/tag/" . str_replace(' ', '-', $tagName[$i]) . "' title='' class='px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200'>#{$tagName[$i]}</a>";
                                }
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
<?php

require_once 'footer.php';

?>