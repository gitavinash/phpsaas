<?php

require_once 'header.php';

?>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex gap-8">
        <!-- Main Discussion Content -->
        <main class="flex-1 max-w-4xl">
            <!-- Discussion Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span
                                class="text-indigo-600 font-medium text-lg"><?php echo (($profile_image != '') ? $profile_image : getInitials($profile_display_name)) ?></span>
                        </div>
                        <div>
                            <div class="flex items-center mb-2">
                                <h2 class="text-xl font-semibold text-gray-900 mr-3"><?php echo $profile_display_name ?>
                                </h2>
                                <!-- <button
                                    class="px-4 py-1.5 bg-primary text-white text-sm rounded-full hover:bg-primary/90 !rounded-button whitespace-nowrap">
                                    <i class="ri-user-add-line mr-1"></i>Follow
                                </button> -->
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <span>Posted <?php echo $published = getPastDuration($link_published); ?></span>
                                <span class="mx-2">•</span>
                                <span
                                    class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full"><?php echo $category_title ?></span>
                                <span class="mx-2">•</span>
                                <div class="flex items-center text-xs">
                                    <i class="ri-pushpin-line mr-1"></i>
                                    <span>Pinned Discussion</span>
                                </div>
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
                <?php

                if ($discussion_status == 1) { ?>
                <h1 class="text-3xl font-bold text-gray-900 mb-6"><?php echo $discussion_title ?></h1>
                <!-- Engagement Stats -->
                <div class="flex items-center justify-between mb-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-8 text-sm text-gray-600">
                        <span class="flex items-center"><i class="ri-eye-line mr-2"></i> 1,247 views</span>
                        <span class="flex items-center"><i class="ri-message-2-line mr-2"></i> 48 comments</span>
                        <span class="flex items-center"><i class="ri-heart-line mr-2"></i> 86 likes</span>
                    </div>
                    <div class="flex items-center space-x-2">
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
                <div class="prose prose-lg max-w-none">
                    <?php

                        echo $discussion_description

                        ?>
                    <!-- Progress Photos -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Progress Photos</h2>
                        <div class="photo-gallery">
                            <?php

                                $gallery = json_decode($gallery_image_list, TRUE);

                                foreach ($gallery as $image) {
                                    echo "<div class='bg-white rounded-lg overflow-hidden shadow-sm border cursor-pointer h-full'>
                                        <img src='https://readdy.ai/api/search-image?query=male%20pattern%20baldness%20before%20treatment%20crown%20view%20thinning%20hair%20receding%20hairline%20medical%20documentation%20clean%20background%20professional%20lighting%20dermatology%20clinic&width=300&height=400&seq=before1&orientation=portrait'
                                            alt='Before - Crown View' class='w-full h-64 object-cover object-top'>
                                        <div class='p-3'>
                                            <p class='font-medium text-gray-900'>Before - Crown View</p>
                                            <p class='text-sm text-gray-500'>February 2024</p>
                                        </div>
                                    </div>";
                                }

                                ?>
                        </div>
                    </div>
                    <!-- Side Effects Section -->
                    <div class="bg-red-50 rounded-lg p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Side Effects Experience</h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    <i class="ri-alert-line text-red-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Decreased Libido (Weeks 2-4)</h3>
                                    <p class="text-gray-700">Experienced about 20-30% reduction in sexual desire.
                                        Resolved completely after dosage adjustment.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    <i class="ri-brain-line text-red-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Mild Brain Fog (Weeks 2-3)</h3>
                                    <p class="text-gray-700">Slight difficulty concentrating during the initial
                                        adjustment period. Completely resolved by month 2.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    <i class="ri-scissors-cut-line text-red-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Initial Shedding (Months 1-2)</h3>
                                    <p class="text-gray-700">Significant increase in daily hair loss, which was
                                        expected and temporary according to my dermatologist.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reaction Buttons -->
                <div class="flex items-center justify-between py-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <button
                            class="flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-full hover:bg-red-100 reaction-btn !rounded-button whitespace-nowrap">
                            <i class="ri-heart-line mr-2"></i>
                            <span>86</span>
                        </button>
                        <button
                            class="flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 reaction-btn !rounded-button whitespace-nowrap">
                            <i class="ri-thumb-up-line mr-2"></i>
                            <span>42</span>
                        </button>
                        <button
                            class="flex items-center px-4 py-2 bg-yellow-50 text-yellow-600 rounded-full hover:bg-yellow-100 reaction-btn !rounded-button whitespace-nowrap">
                            <i class="ri-star-line mr-2"></i>
                            <span>15</span>
                        </button>
                        <button
                            class="flex items-center px-4 py-2 bg-gray-50 text-gray-600 rounded-full hover:bg-gray-100 reaction-btn !rounded-button whitespace-nowrap">
                            <i class="ri-share-line mr-2"></i>
                            Share
                        </button>
                    </div>
                    <button
                        class="flex items-center px-4 py-2 text-gray-600 hover:text-red-600 !rounded-button whitespace-nowrap">
                        <i class="ri-flag-line mr-2"></i>
                        Report
                    </button>
                </div>
                <?php
                } else {
                    echo "Article is not live";
                }

                ?>
            </div>
        </main>
        <!-- Sidebar -->
        <aside class="w-80">
            <!-- Author Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                        <span class="text-indigo-600 font-medium text-lg">EM</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Emily Morgan</h3>
                        <p class="text-sm text-gray-500">Active member since 2023</p>
                        <div class="flex items-center mt-1">
                            <div class="flex text-yellow-400">
                                <i class="ri-star-fill text-xs"></i>
                                <i class="ri-star-fill text-xs"></i>
                                <i class="ri-star-fill text-xs"></i>
                                <i class="ri-star-fill text-xs"></i>
                                <i class="ri-star-line text-xs"></i>
                            </div>
                            <span class="text-xs text-gray-500 ml-2">4.8 rating</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center text-sm">
                    <div>
                        <div class="font-semibold text-gray-900">23</div>
                        <div class="text-gray-500">Posts</div>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">156</div>
                        <div class="text-gray-500">Helpful</div>
                    </div>
                    <!-- <div class="border-l border-gray-200 pl-4">
                        <div class="font-semibold text-gray-900">2.1k</div>
                        <div class="text-gray-500">Followers</div>
                    </div> -->
                </div>
            </div>
            <!-- Related Discussions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Related Discussions</h3>
                <div class="space-y-4">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="text-sm font-medium text-gray-900 hover:text-primary cursor-pointer">My 2-year
                            finasteride + minoxidil journey</h4>
                        <p class="text-xs text-gray-500 mt-1">by Michael Harris • 156 replies</p>
                    </div>
                    <div class="border-b border-gray-100 pb-3">
                        <a href="https://readdy.ai/home/0ed8e6fd-1b57-4961-9c40-f04471f53645/3d16d496-b6c8-4d0a-b501-417eb8e5f03a"
                            data-readdy="true">
                            <h4 class="text-sm font-medium text-gray-900 hover:text-primary cursor-pointer">
                                Finasteride side effects - what to expect</h4>
                        </a>
                        <p class="text-xs text-gray-500 mt-1">by Dr. Robert Chen • 89 replies</p>
                    </div>
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="text-sm font-medium text-gray-900 hover:text-primary cursor-pointer">
                            Before/after: 1 year on the big 3</h4>
                        <p class="text-xs text-gray-500 mt-1">by James Wilson • 234 replies</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 hover:text-primary cursor-pointer">Dosage
                            adjustment success stories</h4>
                        <p class="text-xs text-gray-500 mt-1">by Katherine Lee • 67 replies</p>
                    </div>
                </div>
            </div>
            <!-- Similar Topics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Similar Topics</h3>
                <div class="flex flex-wrap gap-2">
                    <span
                        class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full cursor-pointer">finasteride</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer">side
                        effects</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer">progress
                        photos</span>
                    <span
                        class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer">timeline</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer">dosage</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full cursor-pointer">6
                        months</span>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="/start-a-discussion/"
                        class="w-full flex items-center justify-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 !rounded-button whitespace-nowrap">
                        <i class="ri-add-line mr-2"></i>
                        Start New Discussion
                    </a>
                    <!-- <button
                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 rounded hover:bg-gray-50 !rounded-button whitespace-nowrap">
                        <i class="ri-bookmark-line mr-2"></i>
                        Save for Later
                    </button>
                    <button
                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 rounded hover:bg-gray-50 !rounded-button whitespace-nowrap">
                        <i class="ri-share-line mr-2"></i>
                        Share Discussion
                    </button> -->
                </div>
            </div>
        </aside>
    </div>
</div>
<?php

require_once 'footer.php';

?>