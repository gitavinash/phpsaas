<?php

$assetVersion = static function ($relativePath) {
    $filePath = USER_PATH . 'assets/' . ltrim($relativePath, '/');
    return (string) (@filemtime($filePath) ?: time());
};

$assetRfSuffix = (defined('ASSET_FORCE_REFRESH_TOKEN') && ASSET_FORCE_REFRESH_TOKEN !== '')
    ? '&rf=' . ASSET_FORCE_REFRESH_TOKEN
    : '';

if (ACTIVE_URL != '/thankyou_newsletter/') {
    echo "<section class='newsletter_wrapper'>
    <div class='container container--box'>
        <div class='row'>
            <div class='newsletter'>
                <form action='/thankyou_newsletter/'>
                    <h3>Sign up for concise summaries of powerful fintech insights delivered straight to your
                        inbox every time we publish.</h3>
                    <div class='input_btn'>
                        <input type='text' class='input newsletter_input' id='newsletterEmail' placeholder='Enter your email to subscribe'>
                        <button class='newsletter_btn' id='submitNewsletter' ><i class='bx bxs-paper-plane'></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>";
}

?>
</main>
<footer>
    <div class="footer_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="footer_left">
                        <div class="footer_logo">
                            <a href="/"><img src="<?php echo SITE_LOGO ?>" alt="logo"></a>
                            <p>Building the newsroom of record for Fintech and Emerging tech.</p>
                        </div>
                        <div class="social_links">
                            <h3>Follow us on</h3>
                            <a href="https://twitter.com/theheadandtale"><img
                                    src="<?php echo USER_IMAGES ?>social_media/twitter.png" alt="twitter"></a>
                            <a href="https://www.linkedin.com/company/theheadandtale/"><img
                                    src="<?php echo USER_IMAGES ?>social_media/linkedin.png" alt="linkden"></a>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="footer_right">
                        <div class="page_link">
                            <a href="/about-us/">about us</a>
                            <a href="/terms-and-conditions/">terms and conditions</a>
                            <a href="/privacy-policy/">privacy policy</a>
                            <a href="/refund-policy/">refund policy</a>
                            <a href="/partner-insights-guidelines/">partner insights guidelines</a>
                            <a href="/subscribe/" class="subscribe_btn">subscribe</a>
                            <a href="/corporate-subscription/" class="subscribe_btn"
                                style="color: #fff; background-color: #333;">Corporate Subscription</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="copyright">
                <p>Copyright <?php echo date('Y') ?> @ <?php echo SITE_NAME ?></p>
                <p class="right">Designed and Developed by <a href="https://blumox.com"
                        title="News website development company in new delhi">Blumox
                        Technologies</a></p>
            </div>
        </div>
    </div>
</footer>

<?php require_once NOTIFICATION . 'index.php'; ?>
<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>
<script src="<?php echo USER_JS ?>script.js?v=<?php echo $assetVersion('js/script.js') . $assetRfSuffix; ?>"></script>
<script src="<?php echo USER_JS ?>slider.min.js?v=<?php echo $assetVersion('js/slider.min.js') . $assetRfSuffix; ?>"></script>
<script>
    const searchButton = document.getElementById('searchButton');
    const closeButton = document.getElementById('closeButton');
    const searchContainer = document.querySelector('.search-container');
    const searchInput = document.getElementById('searchInput');

    document.addEventListener('DOMContentLoaded', () => {
        // Check if the device is mobile
        if (window.innerWidth <= 768) {
            searchButton.click(); // Simulate a click on the search button
        }
    });

    // Expand the search area and apply autofocus on mobile
    searchButton.addEventListener('click', () => {
        searchContainer.classList.add('expanded');

        // Ensure the search input has enough time to expand
        // setTimeout(() => {
        //     if (window.innerWidth <= 768) {
        //         searchInput.focus(); // Apply autofocus for mobile devices
        //     }
        // }, 200); // Wait a bit for the expansion animation to complete
    });

    // Close the search area
    closeButton.addEventListener('click', () => {
        searchContainer.classList.remove('expanded');
        searchInput.value = ''; // Clear input
    });

    // Redirect to /search/?q={value} when the "Enter" key is pressed
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && searchInput.value.trim() !== '') {
            const query = encodeURIComponent(searchInput.value.toLowerCase());
            window.location.href = `/search/?q=${query}`;
        }
    });
</script>
</body>

</html>