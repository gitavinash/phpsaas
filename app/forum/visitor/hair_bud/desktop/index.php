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

    <!-- Community Highlights -->
    <section class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Featured Specialists -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Specialists</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <?php

                    if ($doctorList = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "doctor INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=link_id AND link_site_id=" . SITE_ID . " AND link_type='doctor' WHERE doctor_site_id=" . SITE_ID . " LIMIT 0, 6")) {
                        if (mysqli_num_rows($doctorList) != 0) {
                            while ($doctorInfo = mysqli_fetch_assoc($doctorList)) {
                                echo "<div
                        class='bg-white rounded-lg shadow-sm border border-gray-100 p-5 transition-all duration-300 specialist-card'>
                        <div class='flex items-center mb-4'>
                            <img src='https://readdy.ai/api/search-image?query=professional%2520headshot%2520of%2520a%2520middle-aged%2520male%2520doctor%2520with%2520short%2520dark%2520hair%2520and%2520glasses%252C%2520wearing%2520a%2520white%2520coat%252C%2520friendly%2520smile%252C%2520clean%2520medical%2520office%2520background%252C%2520professional%2520lighting%252C%2520high%2520quality%2520portrait&width=80&height=80&seq=1&orientation=squarish'
                                alt='Dr. Michael Thompson' class='w-16 h-16 rounded-full object-cover object-top mr-4'>
                            <div>
                                <div class='flex items-center'>
                                    <h3 class='font-medium text-gray-900'>Dr. Michael Thompson</h3>
                                    <div class='ml-2 w-5 h-5 flex items-center justify-center bg-blue-100 rounded-full'>
                                        <i class='ri-verified-badge-fill text-blue-600 text-xs'></i>
                                    </div>
                                </div>
                                <p class='text-sm text-gray-500'>Hair Transplant Surgeon</p>
                                <div class='flex mt-1'>
                                    <div class='flex'>
                                        <i class='ri-star-fill text-yellow-400'></i>
                                        <i class='ri-star-fill text-yellow-400'></i>
                                        <i class='ri-star-fill text-yellow-400'></i>
                                        <i class='ri-star-fill text-yellow-400'></i>
                                        <i class='ri-star-fill text-yellow-400'></i>
                                    </div>
                                    <span class='text-xs text-gray-500 ml-1'>(127)</span>
                                </div>
                            </div>
                        </div>
                        <p class='text-sm text-gray-600 mb-4'>Specializing in FUE transplants with 15+ years of
                            experience. Board certified with over 2,000 successful procedures.</p>
                        <button
                            class='w-full py-2 bg-primary text-white rounded-md hover:bg-primary/90 !rounded-button whitespace-nowrap'>
                            View Profile
                        </button>
                    </div>";
                            }
                        }
                    }

                    ?>

                </div>
            </div>
            <!-- Success Stories -->
            <div>
                <h2 class='text-2xl font-bold text-gray-900 mb-6'>Recent Success Stories</h2>
                <?php

                if ($getDiscussion = mysqli_query(
                    $connection,
                    "SELECT * FROM " . TABLE_PREFIX . "discussion 
    INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id=discussion_id AND link_site_id=" . SITE_ID . " AND link_type='discussion' 
    INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=link_owner_id 
    WHERE discussion_status=1 AND discussion_type='success_story' AND discussion_site_id=" . SITE_ID . " 
    LIMIT 3"
                )) {

                    if (mysqli_num_rows($getDiscussion) > 0) {
                        echo "<div class='space-y-6'>";

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

                            echo "<div
                        class='bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 success-story-card'>
                        <div class='grid grid-cols-2'>
                            <div class='p-5'>
                                <div class='flex items-center mb-3'>
                                    <div
                                        class='w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3'>
                                        <span class='text-green-600 font-medium'>AK</span>
                                    </div>
                                    <div>
                                        <p class='font-medium text-gray-900'>{$displayName}</p>
                                        <p class='text-xs text-gray-500'>6 months post-procedure</p>
                                    </div>
                                </div>
                                <h3 class='font-medium text-gray-900 mb-2'>{$d['discussion_title']}</h3>
                                <p class='text-sm text-gray-600 mb-3 line-clamp-3'>={$desc}</p>
                                <a href='{$d['link_url']}'
                                    data-readdy='true' class='text-primary text-sm font-medium cursor-pointer hover:underline'>Read
                                    Full Story</a>
                            </div>
                            <div class='bg-gray-100 flex h-64'>
                                <div class='w-1/2 bg-gray-200 flex items-center justify-center overflow-hidden'>
                                    <img src='{$d['discussion_before_image']}' class='w-full h-full object-cover' alt='Before Image'>
                                </div>
                                <div class='w-1/2 bg-gray-50 flex items-center justify-center overflow-hidden'>
                                    <img src='{$d['discussion_after_image']}' class='w-full h-full object-cover' alt='After Image'>
                                </div>
                            </div>
                        </div>
                    </div>";
                        }

                        echo "</div>";
                    }
                }
                ?>
            </div>
    </section>
    <!-- Community Activity -->
    <section class="bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Recent Activity</h2>
                <!-- <div class="flex space-x-2">
                    <button
                        class="px-3 py-1 bg-primary text-white rounded-full text-sm !rounded-button whitespace-nowrap">All</button>
                    <button
                        class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Discussions</button>
                    <button
                        class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Comments</button>
                    <button
                        class="px-3 py-1 bg-white text-gray-700 rounded-full text-sm !rounded-button whitespace-nowrap">Questions</button>
                </div> -->
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <?php

                if ($recentActivity = mysqli_query(
                    $connection,
                    "SELECT * FROM " . TABLE_PREFIX . "activity WHERE activity_site_id=" . SITE_ID . " ORDER BY activity_time DESC"
                )) {
                    if (mysqli_num_rows($recentActivity) != 0) {
                        while ($recentActivityData = mysqli_fetch_assoc($recentActivity)) {
                            echo "<div class='p-4 border-b border-gray-100 transition-all duration-200 activity-item'>
                    <div class='flex'>
                        <div
                            class='w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0'>
                            <span class='text-green-600 font-medium'>DR</span>
                        </div>
                        <div>
                            <div class='flex items-center'>
                                <p class='font-medium text-gray-900'>Dr. Robert Chen</p>
                                <div class='ml-1 w-4 h-4 flex items-center justify-center bg-blue-100 rounded-full'>
                                    <i class='ri-verified-badge-fill text-blue-600 text-xs'></i>
                                </div>
                                <span class='mx-2 text-gray-400'>•</span>
                                <p class='text-gray-500 text-sm'>Replied to a discussion</p>
                                <span class='mx-2 text-gray-400'>•</span>
                                <p class='text-gray-400 text-sm'>32 minutes ago</p>
                            </div>
                            <h4 class='font-medium text-gray-800 mt-1'>Re: Is 25 too young for a hair transplant?
                            </h4>
                            <p class='text-sm text-gray-600 mt-1'>While there's no strict age minimum for hair
                                transplants, at 25 it's important to consider that your hair loss pattern may still
                                be
                                evolving. I typically recommend stabilizing loss with medications first...</p>
                            <div class='flex mt-2'>
                                <span
                                    class='px-2.5 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full'>Transplant
                                    Procedures</span>
                            </div>
                        </div>
                    </div>
                </div>";
                        }
                    }
                }

                ?>
            </div>
            <div class="text-center mt-6">
                <button
                    class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 !rounded-button whitespace-nowrap">
                    Load More <i class="ri-arrow-down-s-line ml-1"></i>
                </button>
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
                <a href="/login/"
                    class="px-6 py-3 bg-transparent border-2 border-white text-white font-medium rounded-md hover:bg-white/10 !rounded-button whitespace-nowrap">
                    Create Account
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>