<?php require_once 'header.php'; ?>
<main class="pt-28 pb-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Article Section -->
            <div class="lg:w-3/4 w-full">
                <h1 class="text-2xl font-bold mb-6">
                    Search Results for: <span class="text-primary"><?php echo ucwords($searchKeyword); ?></span>
                </h1>

                <?php
                if (is_array($queryPost) && count($queryPost) > 0) {
                    $tagIds = array_column($queryPost, 'tag_relation_id');
                    $resultTags = implode(' OR link_relation_id=', $tagIds);

                    $postQuery = "
                        SELECT 
                            link_url, 
                            post_title, 
                            post_image,
                            link_published, 
                            meta_description,
                            profile_display_name,
                            profile_image
                        FROM " . TABLE_PREFIX . "link 
                        INNER JOIN " . TABLE_PREFIX . "post ON link_relation_id=post_id 
                        INNER JOIN " . TABLE_PREFIX . "meta ON meta_relation_id=link_id 
                        INNER JOIN " . TABLE_PREFIX . "profile ON profile_user_id=link_owner_id 
                        WHERE 
                            link_site_id=" . SITE_ID . " 
                            AND link_type='post' 
                            AND post_type='blog'
                            AND link_status=200 
                            AND (link_relation_id={$resultTags}) 
                        ORDER BY link_published DESC 
                        LIMIT 100";

                    $result = mysqli_query($connection, $postQuery);
                    $postResults = [];

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $postResults[] = $row;
                        }
                    }

                    if (!empty($postResults)) {
                        echo "<div class='grid grid-cols-1 md:grid-cols-2 gap-8'>";

                        foreach ($postResults as $post) {
                            $title = htmlspecialchars($post['post_title']);
                            $url = htmlspecialchars($post['link_url']);
                            $image = htmlspecialchars($post['post_image']);
                            $desc = htmlspecialchars($post['meta_description']);
                            $date = date('M d, Y', strtotime($post['link_published']));
                            $authorImage = htmlspecialchars($post['profile_image']);
                            $author = htmlspecialchars($post['profile_display_name']);

                            echo "
                            <article class='bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100'>
                                <a href='{$url}' target='_blank'>
                                    <img src='{$image}' alt='{$title}' class='w-full h-56 object-cover'>
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
                        echo "<p class='text-gray-600 text-center mt-10'>No post articles found for your search.</p>";
                    }
                } else {
                    echo "<p class='text-gray-600 text-center mt-10'>No results found for your search.</p>";
                }
                ?>
            </div>

            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>