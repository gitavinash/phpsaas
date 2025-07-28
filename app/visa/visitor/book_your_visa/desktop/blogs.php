<?php require_once 'header.php'; ?>

<main class="pt-28 pb-16">
    <div class="container mx-auto px-4">
        <?php
        $latestPostData = [];

        $query = "
            SELECT 
                link_is_featured, 
                link_modified, 
                post_title, 
                post_image, 
                post_description, 
                link_url, 
                link_published,
                meta_description,
                profile_display_name,
                profile_image
            FROM " . TABLE_PREFIX . "post 
            INNER JOIN " . TABLE_PREFIX . "link ON link_relation_id = post_id 
            INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id = link_id 
            INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id = link_owner_id 
            WHERE 
                link_type = 'post'
                AND post_type='blog'
                AND link_status = 200 
                AND link_site_id = " . SITE_ID . "
            ORDER BY 
                link_is_featured DESC, 
                link_published DESC
            LIMIT 20";

        $result = mysqli_query($connection, $query);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $latestPostData[] = $row;
            }
        } else {
            echo "<p class='text-red-600'>Error loading post: " . mysqli_error($connection) . "</p>";
        }

        // Helper to render cards
        function renderPostCardTailwind($data, $isFeatured = false)
        {
            $formattedDate = date('d M Y h:i:A', strtotime($data['link_published']));
            $postImage = htmlspecialchars($data['post_image']);
            $postTitle = htmlspecialchars($data['post_title']);
            $postUrl = htmlspecialchars($data['link_url']);
            $metaDescription = htmlspecialchars($data['meta_description']);
            $profileImage = htmlspecialchars($data['profile_image']);
            $profileDisplayName = htmlspecialchars($data['profile_display_name']);

            if ($isFeatured) {
                echo "
                <a href='{$postUrl}' title='{$postTitle}'>
                    <article class='bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 mb-12'>
                    <div class='relative'>
                        <img src='{$postImage}' alt='{$postTitle}' class='w-full h-[400px] object-cover'>
                        <div class='absolute inset-0 bg-gradient-to-t from-black/60 to-transparent'></div>
                        <div class='absolute bottom-0 left-0 right-0 p-8 text-white'>
                            <div class='flex items-center gap-2 mb-4'>
                                <span class='text-sm'>{$formattedDate}</span>
                            </div>
                            <h1 class='text-3xl font-bold mb-4'>{$postTitle}</h1>
                            <p class='text-lg mb-6 opacity-90'>{$metaDescription}</p>
                            <a href='{$postUrl}' title='{$postTitle}'>
                                <button class='px-6 py-2 bg-white text-gray-900 font-medium !rounded-button hover:bg-gray-100'>
                                    Read Article
                                </button>
                            </a>
                        </div>
                    </div>
                </article>
                </a>";
            } else {
                echo "
                <article class='bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100'>
                <a href='{$postUrl}' title='{$postTitle}'>
                    <img src='{$postImage}' alt='{$postTitle}' class='w-full h-56 object-cover'>
                    <div class='p-6'>
                        <div class='flex items-center gap-2 mb-4'>
                            <span class='text-sm text-gray-500'>{$formattedDate}</span>
                        </div>
                        <h2 class='text-xl font-semibold mb-2 line-clamp-2'>{$postTitle}</h2>
                        <p class='text-gray-600 mb-4 line-clamp-3'>{$metaDescription}</p>
                        <div class='flex items-center justify-between'>
                            <div class='flex items-center gap-3'>
                                <div class='w-8 h-8 rounded-full bg-gray-200'>
                                    <img src='{$profileImage}' alt='{$profileDisplayName}' class='w-full h-full object-cover rounded-full'>
                                </div>
                                <div>
                                    <div class='text-sm font-medium'>{$profileDisplayName}</div>
                                    <div class='text-xs text-gray-500'>{$formattedDate}</div>
                                </div>
                            </div>
                            <a href='{$postUrl}' title='{$postTitle}' class='text-primary hover:text-primary/80'>Read More</a>
                        </div>
                    </div>
                    </a>
                </article>";
            }
        }
        ?>

        <?php if (!empty($latestPostData)) : ?>
        <!-- Featured Post -->
        <?php renderPostCardTailwind($latestPostData[0], true); ?>

        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-3/4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php
                        for ($i = 1; $i < count($latestPostData); $i++) {
                            renderPostCardTailwind($latestPostData[$i]);
                        }
                        ?>
                </div>
            </div>
            <?php require_once 'sidebar.php'; ?>
        </div>
        <?php else : ?>
        <p class="text-gray-600 text-center">No post articles available at the moment.</p>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'footer.php'; ?>