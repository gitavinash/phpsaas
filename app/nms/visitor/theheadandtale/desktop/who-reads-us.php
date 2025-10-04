<?php
require_once DATA_FUNCTIONS;
require_once 'header.php';
?>

<section>
    <div class="row">
        <div class="container">
            <div class="left-container" style="width:100%;">

                <div class="categoryHeading">
                    <h1>Who Reads Us</h1>
                </div>

                <?php
                // zyada fetch kar lo; agar function "all" support karta ho to uska use kar lo
                $testimonials = testimonialsList($connection, 1000);

                // helper: quote ko saaf karo (inline spans, nbsp, etc.)
                function clean_quote($s)
                {
                    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
                    $s = strip_tags($s);                 // remove inline styles/tags
                    $s = preg_replace('/\xC2\xA0|&nbsp;?/i', ' ', $s); // nbsp
                    $s = preg_replace('/\s{2,}/', ' ', $s);            // extra spaces
                    return trim($s);
                }

                if (!empty($testimonials)) {
                    echo "<div class='t-grid'>";
                    foreach ($testimonials as $row) {

                        $readerInfo     = explode(",", $row["category_title"]);
                        $readerName     = trim($readerInfo[0]);
                        $readerPosition = ucwords(trim(str_replace($readerName . (sizeof($readerInfo) == 1 ? "" : ","), "", $row["category_title"])));

                        $img   = ($row['category_image'] != '' ? $row['category_image'] : IMG_LIBRARY . 'default-black-profile-pic-icon.png');
                        $quote = clean_quote($row['category_description']);

                        // escape safe
                        $nameEsc  = htmlspecialchars($readerName, ENT_QUOTES, 'UTF-8');
                        $roleEsc  = htmlspecialchars($readerPosition, ENT_QUOTES, 'UTF-8');
                        $imgEsc   = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                        $quoteEsc = htmlspecialchars($quote, ENT_QUOTES, 'UTF-8');

                        echo "
              <article class='t-card'>
                <header class='t-header'>
                  <img class='t-avatar' src='{$imgEsc}' alt='{$nameEsc}'>
                  <div class='t-meta'>
                    <div class='t-name'>{$nameEsc}</div>
                    <div class='t-role'>{$roleEsc}</div>
                  </div>
                </header>
                <p class='t-quote'>{$quoteEsc}</p>
              </article>";
                    }
                    echo "</div>";
                }
                ?>

            </div>
            <!-- sidebar intentionally hidden for this page -->
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>