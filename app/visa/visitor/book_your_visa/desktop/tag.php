<?php require_once 'header.php'; ?>
<main class="pt-28 pb-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Article Section -->
            <div class="lg:w-3/4 w-full">
                <h1 class="text-2xl font-bold mb-6">
                    <?php echo htmlspecialchars(ucwords($tag_title)); ?>
                </h1>

                <?php
                $postResults = [];

                if (is_array($tagPost) && count($tagPost) > 0) {
                    $tagIds = array_column($tagPost, 'tag_relation_id');
                    $resultTags = implode(',', array_map('intval', $tagIds)); // ensure safe integers

                    $postQuery = "
                        SELECT 
                            link_url, 
                            post_title, 
                            post_image,
                            meta_description, 
                            profile_display_name,
                            profile_image,
                            link_published
                        FROM " . TABLE_PREFIX . "link 
                        INNER JOIN " . TABLE_PREFIX . "post ON link_relation_id=post_id 
                        INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
                        INNER JOIN " . TABLE_PREFIX . "profile ON post_owner_id=profile_user_id 
                        WHERE 
                            link_site_id=" . SITE_ID . " 
                            AND link_type='post' 
                            AND link_status=200 
                            AND link_relation_id IN ({$resultTags}) 
                        ORDER BY link_published DESC 
                        LIMIT 20";

                    $result = mysqli_query($connection, $postQuery);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $postResults[] = $row;
                        }
                    } else {
                        echo "<p class='text-red-600'>Error: " . htmlspecialchars(mysqli_error($connection)) . "</p>";
                    }
                }

                if (!empty($postResults)) {
                    echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-8'>";

                    foreach ($postResults as $post) {
                        $title = htmlspecialchars($post['post_title']);
                        $url = htmlspecialchars($post['link_url']);
                        $image = htmlspecialchars($post['post_image']);
                        $desc = htmlspecialchars($post['meta_description']);
                        $authorImage = htmlspecialchars($post['profile_image']);
                        $author = htmlspecialchars($post['profile_display_name']);
                        $date = date('M d, Y', strtotime($post['link_published']));

                        echo "
                        <article class='bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100'>
                            <a href='{$url}' target='_blank'>
                                <div class='relative'>
                                    <img src='{$image}' alt='{$title}' class='w-full h-56 object-cover'>
                                </div>
                                <div class='p-6'>
                                    <div class='flex items-center gap-2 mb-4'>
                                        <span class='text-sm text-gray-500'>{$date}</span>
                                    </div>
                                    <h2 class='text-xl font-semibold mb-2 line-clamp-2'>{$title}</h2>
                                    <p class='text-gray-600 mb-4 line-clamp-3'>{$desc}</p>
                                    <div class='flex items-center justify-between'>
                                        <div class='flex items-center gap-3'>
                                            <div class='w-8 h-8 rounded-full bg-gray-200'>
                                                <img src='{$authorImage}' alt='{$author}' class='w-full h-full object-cover rounded-full'>
                                            </div>
                                            <div>
                                                <div class='text-sm font-medium'>{$author}</div>
                                                <div class='text-xs text-gray-500'>{$date}</div>
                                            </div>
                                        </div>
                                        <span class='text-primary hover:text-primary/80'>Read More</span>
                                    </div>
                                </div>
                            </a>
                        </article>";
                    }

                    echo "</div>";
                } else {
                    echo "<p class='text-gray-600 text-center mt-10'>No articles found for this tag.</p>";
                }
                ?>
            </div>

            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>