<?php require_once 'header.php'; ?>

<main class="pt-28 pb-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Blog Post Grid (3/4) -->
            <div class="lg:w-3/4">
                <h1 class="text-2xl font-bold mb-6">
                    Category: <span class="text-primary"><?php echo ucwords($category_title); ?></span>
                </h1>
                <?php
                $latestPostData = [];

                $query = "
                    SELECT 
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
                        AND post_category = $category_id
                        AND link_status = 200 
                        AND link_site_id = " . SITE_ID . "
                    ORDER BY link_published DESC
                    LIMIT 20";

                $result = mysqli_query($connection, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $latestPostData[] = $row;
                    }
                } else {
                    echo "<p class='text-red-600'>Error loading posts: " . mysqli_error($connection) . "</p>";
                }

                function renderPostCardTailwind($data)
                {
                    $date = date('M d, Y', strtotime($data['link_published']));
                    $image = htmlspecialchars($data['post_image']);
                    $title = htmlspecialchars($data['post_title']);
                    $url = htmlspecialchars($data['link_url']);
                    $desc = htmlspecialchars($data['meta_description']);
                    $authorImage = htmlspecialchars($data['profile_image']);
                    $author = htmlspecialchars($data['profile_display_name']);

                    echo "
                    <article class='bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100'>
                        <a href='{$url}' title='{$title}'>
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
                ?>

                <?php if (!empty($latestPostData)) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($latestPostData as $post) {
                            renderPostCardTailwind($post);
                        } ?>
                </div>
                <?php else : ?>
                <p class="text-gray-600 text-center mt-10">No post articles available at the moment.</p>
                <?php endif; ?>
            </div>

            <?php require_once 'sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once 'footer.php'; ?>