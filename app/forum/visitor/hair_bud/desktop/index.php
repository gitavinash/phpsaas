<?php

require_once 'header.php';

?>
<main>
    <!-- Hero Banner -->
    <section class="relative bg-gradient-to-r from-indigo-600 to-primary overflow-hidden">
        <div class="absolute inset-0"
            style="background-image: url('https://readdy.ai/api/search-image?query=modern%2520abstract%2520background%2520with%2520soft%2520blue%2520and%2520purple%2520gradient%2520waves%252C%2520subtle%2520pattern%2520of%2520hair%2520strands%2520in%2520light%2520colors%252C%2520professional%2520clean%2520design%252C%2520soft%2520lighting%252C%2520minimal%2520style%252C%2520perfect%2520for%2520hair%2520forum%2520website%2520banner&width=1600&height=600&seq=1&orientation=landscape'); background-size: cover; background-position: center; opacity: 0.2;">
        </div>
        <div class="container mx-auto px-4 py-16 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Welcome to HairBud Forum</h1>
                <p class="text-xl text-indigo-100 mb-8">Join our community of hair enthusiasts, specialists, and
                    individuals on their hair restoration journey. Share experiences, get advice, and discover
                    solutions.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="/start-a-discussion/" data-readdy="true"
                        class="px-6 py-3 bg-white text-primary font-medium rounded-md hover:bg-gray-100 shadow-lg !rounded-button whitespace-nowrap">
                        Start a Discussion
                    </a>
                    <a href="/find-a-specialist/"
                        class="px-6 py-3 bg-transparent border-2 border-white text-white font-medium rounded-md hover:bg-white/10 !rounded-button whitespace-nowrap">
                        Find a Specialist
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- Quick Stats Section -->
    <section class="container mx-auto px-4 py-8 -mt-10 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                    <i class="ri-user-3-line ri-lg text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">10</h3>
                    <p class="text-gray-600">Active Members</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                    <i class="ri-discuss-line ri-lg text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">0</h3>
                    <p class="text-gray-600">Total Discussions</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                    <i class="ri-medicine-bottle-line ri-lg text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">0</h3>
                    <p class="text-gray-600">Verified Specialists</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                    <i class="ri-award-line ri-lg text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">1</h3>
                    <p class="text-gray-600">Success Stories</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Featured Discussions -->

    <?php


    if ($getDiscussion = mysqli_query(
        $connection,
        "SELECT * FROM " . TABLE_PREFIX . "discussion 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=discussion_id AND link_site_id=" . SITE_ID . " AND link_type='discussion' 
    INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=link_owner_id 
    WHERE discussion_status=1 AND discussion_site_id=" . SITE_ID . " 
    LIMIT 3"
    )) {

        if (mysqli_num_rows($getDiscussion) > 0) {
            echo "<section class='container mx-auto px-4 py-12'>
            <div class='flex justify-between items-center mb-8'>
                <h2 class='text-2xl font-bold text-gray-900'>Trending Discussions</h2>
                <a href='/trending-discussions/' class='text-primary font-medium flex items-center hover:underline'>
                    View All <i class='ri-arrow-right-line ri-lg ml-1'></i>
                </a>
            </div>
            <div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch'>";

            while ($d = mysqli_fetch_assoc($getDiscussion)) {
                $displayName = $d['profile_display_name'] ?: 'N/A';
                $published = getPastDuration($d['link_published']);
                $type = ucwords(str_replace('_', ' ', $d['discussion_type']));
                $title = htmlspecialchars($d['discussion_title']);
                $url = htmlspecialchars($d['link_url']);
                $desc = htmlspecialchars(truncateText(strip_tags($d['discussion_description'])));
                // Hardcoded example stats, replace with real data if available
                $views = '1.2k';
                $comments = '48';
                $likes = '86';

                echo "
            <a href='{$url}' title='{$title}'>
                <div class='bg-white rounded-lg shadow-sm border border-gray-100 p-6 transition-all duration-300 discussion-card flex flex-col h-full'>
                    <div class='flex items-center mb-4'>
                        <div class='w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3'>
                            <span class='text-indigo-600 font-medium'>EM</span>
                        </div>
                        <div>
                            <p class='font-medium text-gray-900'>{$displayName}</p>
                            <p class='text-xs text-gray-500'>Posted {$published}</p>
                        </div>
                        <div class='ml-auto'>
                            <span class='px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full'>{$type}</span>
                        </div>
                    </div>
                    <h3 class='text-lg font-semibold text-gray-900 mb-2'>{$title}</h3>
                    <p class='text-gray-600 text-sm mb-4 line-clamp-3 flex-grow'>{$desc}</p>
                    <div class='flex justify-between items-center text-sm text-gray-500 mt-auto'>
                        <div class='flex items-center space-x-4'>
                            <span class='flex items-center'><i class='ri-eye-line mr-1'></i> {$views}</span>
                            <span class='flex items-center'><i class='ri-message-2-line mr-1'></i> {$comments}</span>
                        </div>
                        <div class='flex items-center'>
                            <i class='ri-heart-line mr-1'></i> {$likes}
                        </div>
                    </div>
                </div>
            </a>";
            }

            echo "</div></section>";
        }
    }
    ?>

    <!-- Category Showcase -->
    <section class="bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Explore Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <?php

                $featureCategory = getCategoryWithTotalPost($connection, 'discussion', 6);

                foreach ($featureCategory as $categoryData) {
                    extract($categoryData);
                    echo "<a href='{$link_url}'
                        class='bg-white rounded-lg shadow-sm p-6 text-center transition-all duration-300 category-card'>
                        <div class='w-14 h-14 mx-auto rounded-full bg-indigo-100 flex items-center justify-center mb-4'>
                            " . (($category_image != '') ? "<img src='{$category_image}' alt='{$category_title}' title='{$category_title}'>" : "<i class='ri-medicine-bottle-line ri-xl text-purple-600'></i>") . "
                        </div>
                        <h3 class='font-medium text-gray-900 mb-1'>{$category_title}</h3>
                        <p class='text-sm text-gray-500'>{$totalCount} posts</p>
                    </a>";
                }

                ?>
            </div>
        </div>
    </section>

    <!-- Join Banner -->
    <section class="bg-gradient-to-r from-primary to-indigo-700 py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Join Our Growing Community</h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">Connect with specialists and others on similar
                journeys. Share experiences, get advice, and discover the latest hair restoration solutions.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/start-a-discussion/" data-readdy="true"
                    class="px-6 py-3 bg-white text-primary font-medium rounded-md hover:bg-gray-100 shadow-lg !rounded-button whitespace-nowrap">
                    Start a Discussion
                </a>
                <button
                    class="px-6 py-3 bg-transparent border-2 border-white text-white font-medium rounded-md hover:bg-white/10 !rounded-button whitespace-nowrap">
                    Create Account
                </button>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>