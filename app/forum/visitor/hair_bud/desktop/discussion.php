<?php

require_once DATA_FUNCTIONS;
require_once 'header.php';

?>
<!-- Main Content -->
<div class="container max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Discussion Content -->
        <main class="w-full lg:flex-1 max-w-full lg:max-w-4xl">
            <!-- Discussion Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4 sm:gap-0">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-indigo-600 font-medium text-lg">
                                <?php echo (($profile_image != '') ? $profile_image : getInitials($profile_display_name)) ?>
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center mb-2 flex-wrap">
                                <h2 class="text-xl font-semibold text-gray-900 mr-3">
                                    <?php echo ucwords($profile_display_name) ?>
                                </h2>
                            </div>
                            <div class="flex flex-wrap items-center text-sm text-gray-500 gap-2">
                                <span>Posted <?php echo $published = getPastDuration($link_published); ?></span>
                                <span class="mx-2 hidden sm:inline">•</span>
                                <span
                                    class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full"><?php echo $category_title ?></span>
                                <?php
                                if ($link_is_featured == 1) {
                                    echo "<span class='mx-2 hidden sm:inline'>•</span><div class='flex items-center text-xs'><i class='ri-pushpin-line mr-1'></i><span>Pinned Discussion</span></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button
                            class="flex items-center px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded !rounded-button whitespace-nowrap">
                            <i class="ri-more-line"></i>
                        </button>
                    </div>
                </div>
                <?php if ($discussion_status == 1) { ?>
                <h1 class="text-3xl font-bold text-gray-900 mb-6"><?php echo $discussion_title ?></h1>

                <!-- Engagement Stats -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between mb-8 p-4 bg-gray-50 rounded-lg gap-4">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <span class="flex items-center"><i class="ri-eye-line mr-2"></i> 1,247 views</span>
                        <span class="flex items-center"><i class="ri-message-2-line mr-2"></i> 48 comments</span>
                        <span class="flex items-center"><i class="ri-heart-line mr-2"></i> 86 likes</span>
                    </div>
                    <div class="flex items-center space-x-2 flex-wrap sm:flex-nowrap">
                        <button
                            class="flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-full hover:bg-gray-50 vote-btn transition-transform">
                            <i class="ri-arrow-up-line text-gray-600"></i>
                        </button>
                        <span class="font-medium text-gray-900">+42</span>
                        <button
                            class="flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-full hover:bg-gray-50 vote-btn transition-transform">
                            <i class="ri-arrow-down-line text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="prose prose-lg max-w-none custom-prose mb-4">
                    <?php echo $discussion_description ?>
                </div>

                <?php
                    if ($gallery_image_list != '') {
                        $gallery = json_decode($gallery_image_list, true);

                        if (is_array($gallery) && count($gallery) > 0) {
                            echo '<div class="mb-8">';
                            echo '<h2 class="text-2xl font-bold text-gray-900 mb-6">Progress Photos</h2>';
                            echo '<div class="photo-gallery grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">';
                            foreach ($gallery as $image) {
                                $url = htmlspecialchars($image['path'] ?? '', ENT_QUOTES);
                                $title = htmlspecialchars($image['name'] ?? 'Progress Photo', ENT_QUOTES);

                                if (!$url) continue;

                                echo "
                                    <div class='bg-white rounded-lg overflow-hidden shadow-sm border cursor-pointer h-full flex flex-col'>
                                        <img src='{$url}' alt='{$title}' class='w-full h-64 object-cover object-top' loading='lazy'>
                                    </div>
                                ";
                            }
                            echo '</div></div>';
                        }
                    }
                    ?>

                <!-- Reaction Buttons -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between py-6 border-t border-gray-200 gap-4">
                    <div class="flex items-center space-x-4">
                        <button onclick="shareDiscussion()"
                            class="flex items-center px-4 py-2 bg-gray-50 text-gray-600 rounded-full hover:bg-gray-100 reaction-btn !rounded-button whitespace-nowrap">
                            <i class="ri-share-line mr-2"></i> Share
                        </button>
                    </div>
                </div>
                <?php } else {
                    echo "Article is not live";
                } ?>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="w-full lg:w-80">
            <!-- Author Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <?php
                $authorInfo = authorInfo($connection, $linkOwnerId);
                foreach ($authorInfo as $authorData) {
                    echo "<div class='flex items-center mb-4'>
                            <div class='w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mr-4'>
                                <span class='text-indigo-600 font-medium text-lg'>" . getInitials($authorData['profile_display_name']) . "</span>
                            </div>
                            <div>
                                <h3 class='font-semibold text-gray-900'>" . ucwords($authorData['profile_display_name']) . "</h3>
                                <p class='text-sm text-gray-500'>Active member since " . date('Y', strtotime($authorData['user_reg_time'])) . "</p>
                            </div>
                        </div>
                        <div class='grid grid-cols-3 gap-4 text-center text-sm'>
                            <div>
                                <div class='font-semibold text-gray-900'>{$authorData['totalPost']}</div>
                                <div class='text-gray-500'>Posts</div>
                            </div>
                        </div>";
                }
                ?>
            </div>

            <!-- Related Discussions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Related Discussions</h3>
                <div class="space-y-4">
                    <?php
                    $similarArticles = similarArticles($connection, $discussion_id, $postType, 10);
                    foreach ($similarArticles['articles'] as $artcileInfo) {
                        echo "<div class='border-b border-gray-100 pb-3 break-words'>
                                <a href='{$artcileInfo['link_url']}' data-readdy='true'>
                                    <h4 class='text-sm font-medium text-gray-900 hover:text-primary cursor-pointer'>{$artcileInfo['title']}t</h4>
                                </a>
                                <p class='text-xs text-gray-500 mt-1'>by " . ucwords($artcileInfo['profile_display_name']) . "</p>
                            </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Similar Topics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Similar Topics</h3>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $tagName = explode(', ', $tagName);
                    foreach ($tagName as $tag) {
                        echo "<span class='px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer'>
                            <a href='/tag/" . str_replace(' ', '-', $tag) . "' title='" . ucwords($tag) . "'>{$tag}</a>
                        </span>";
                    }
                    ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3 sm:space-y-0 sm:space-x-3 sm:flex">
                    <a href="/start-a-discussion/"
                        class="w-full flex items-center justify-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 !rounded-button whitespace-nowrap">
                        <i class="ri-add-line mr-2"></i>
                        Start New Discussion
                    </a>
                    <button onclick="shareDiscussion()"
                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 rounded hover:bg-gray-50 !rounded-button whitespace-nowrap">
                        <i class="ri-share-line mr-2"></i>
                        Share Discussion
                    </button>
                </div>
                <div id="share-fallback" class="mt-4 p-4 border rounded bg-white shadow-md hidden">
                    <p class="text-sm mb-2 font-semibold">Share via:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        <button onclick="copyLink()"
                            class="flex items-center gap-2 px-3 py-2 border rounded text-gray-700 hover:bg-gray-100">
                            <i class="ri-link"></i> Copy Link
                        </button>
                        <a id="twitter-share" target="_blank"
                            class="flex items-center gap-2 px-3 py-2 border rounded text-blue-500 hover:bg-gray-100">
                            <i class="ri-twitter-x-line"></i> Twitter
                        </a>
                        <a id="facebook-share" target="_blank"
                            class="flex items-center gap-2 px-3 py-2 border rounded text-blue-700 hover:bg-gray-100">
                            <i class="ri-facebook-circle-line"></i> Facebook
                        </a>
                        <a id="linkedin-share" target="_blank"
                            class="flex items-center gap-2 px-3 py-2 border rounded text-blue-800 hover:bg-gray-100">
                            <i class="ri-linkedin-box-line"></i> LinkedIn
                        </a>
                        <a id="email-share"
                            class="flex items-center gap-2 px-3 py-2 border rounded text-gray-800 hover:bg-gray-100">
                            <i class="ri-mail-line"></i> Email
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php require_once 'footer.php'; ?>