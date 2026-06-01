<aside class="sidebar">
    <div class="sidebar-widget">
      <h3>Search</h3>
      <form method="GET" action="/search/" style="display: flex">
        <input
          type="text"
          placeholder="Search..."
          name="q"
          style="
                width: 100%;
                padding: 0.8rem;
                border: 1px solid #ddd;
                border-radius: 5px 0 0 5px;
                outline: none;
              " />
        <button
          class="btn btn-primary"
          style="border-radius: 0 5px 5px 0; padding: 0 1rem">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    <div class="sidebar-widget">
      <h3>Categories</h3>
      <ul class="widget-links">
        <?php
        $query =
            "
    SELECT 
        l.link_url,
        c.category_id,
        c.category_title,
        COUNT(p.post_id) AS total_posts
    FROM " .
            TABLE_PREFIX .
            "link l
    INNER JOIN " .
            TABLE_PREFIX .
            "category c
        ON c.category_id = l.link_relation_id
    LEFT JOIN " .
            TABLE_PREFIX .
            "post p
        ON p.post_category = c.category_id
    WHERE l.link_site_id = '" .
            SITE_ID .
            "'
      AND l.link_type = 'category'
      AND c.category_type = 'post'
    GROUP BY c.category_id, c.category_title, l.link_url
    ORDER BY c.category_title ASC
";

        if ($categoryList = mysqli_query($connection, $query)) {
            if (mysqli_num_rows($categoryList) > 0) {
                while ($category = mysqli_fetch_assoc($categoryList)) {
                    extract($category);
                    echo "<li>
                      <a href='{$link_url}'>" .
                        ucwords($category_title) .
                        " <span>({$total_posts})</span></a>
                    </li>";
                }
            }
        }
        ?>
      </ul>
    </div>

   <?php
   
   $ignoreCurrentPost = isset($post_id) ? "AND post_id != '{$post_id}'" : '';

   if (
    
       $recentPost = mysqli_query(
           $connection,
           'SELECT link_url, post_title, post_time, post_image FROM ' .
               TABLE_PREFIX .
               'post p JOIN ' .
               TABLE_PREFIX .
               "link l ON l.link_relation_id = p.post_id WHERE l.link_site_id='" .
               SITE_ID .
               "' AND l.link_type='post' {$ignoreCurrentPost} ORDER BY link_published DESC LIMIT 5",
       )
   ) {
       if (mysqli_num_rows($recentPost) > 0) {
           echo '<div class="sidebar-widget">
        <h3>Recent Posts</h3>';
           while ($post = mysqli_fetch_assoc($recentPost)) {
               extract($post);
               echo "

            <a
              href='{$link_url}'
              style='color: var(--text-main); text-decoration: none'>
      <div style='display: flex; gap: 1rem; margin-bottom: 1rem'>
        <img
          src='{$post_image}'
          alt='{$post_title}'
          style='
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 5px;
              ' />
        <div>
          <h5 style='margin-bottom: 0.3rem'>{$post_title}
          </h5>
          <span style='font-size: 0.8rem; color: #888'>" .
                   date('M j, Y', strtotime($post_time)) .
                   "</span>
        </div>
      </div></a>";
           }
           echo '</div>';
       }
   } ?>

    <div
      class="sidebar-widget"
      style="background: var(--primary-color); color: white">
      <h3
        style="color: white; border-bottom-color: rgba(255, 255, 255, 0.3)">
        Join Newsletter
      </h3>
      <p style="margin-bottom: 1rem; opacity: 0.9">
        Stay updated with our latest news and impact stories.
      </p>
      <input
        type="email"
        placeholder="Your Email Address"
        style="
              width: 100%;
              padding: 0.8rem;
              border: none;
              border-radius: 5px;
              margin-bottom: 0.8rem;
            " />
      <button class="btn btn-secondary" style="width: 100%">
        Subscribe
      </button>
    </div>
  </aside>