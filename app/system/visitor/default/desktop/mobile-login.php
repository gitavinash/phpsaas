<?php

$redirect_url = ((isset($redirect_url)) ? $redirect_url : '/dashboard/');

if (isset($_COOKIE['isLogin'])) {

    header("Location: {$redirect_url}");
    exit();
}

$quoteOfTheDay['quoteText'] = '';
$quoteOfTheDay['quoteAuthor'] = '';

if (SITE_VERSION == 'desktop') {
    $context = stream_context_create([
        'http' => [
            'timeout' => 60
        ]
    ]);

    $quoteOfTheDay = file_get_contents("https://api.forismatic.com/api/1.0/?method=getQuote&format=json&key=1&lang=en", false, $context);
    $quoteOfTheDay = json_decode($quoteOfTheDay, TRUE);
}

require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <div class="slide">
            <div class="blockquote-wrapper">
                <div class="blockquote">
                    <?php

                    if ($quoteOfTheDay['quoteText'] != '') {
                        echo '<p>' . $quoteOfTheDay['quoteText'] . '</p>';
                    }
                    if ($quoteOfTheDay['quoteAuthor'] != '') {
                        echo '<p><strong>&mdash; ' . $quoteOfTheDay['quoteAuthor'] . '</strong></p>';
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="col form-bg">
    <div class="form">
        <div class="logo"><img src="<?php echo SITE_LOGO ?>" alt="<?php echo SITE_NAME ?>"></div>
        <div class="card-body">
            <div class="form-heading">
                <strong>Welcome!</strong> Enter your phone number to get started.
            </div>
            <form id="login" autocomplete="off">
                <div class="form-group">
                    <input type="number" class="form-control input-with-label" name="mobile" id="mobile" required
                        placeholder="Enter your mobile number" value="<?php echo ((isset($mobile)) ? $mobile : '') ?>">
                </div>
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>">
                <button type="submit" id="mobileLoginContinueBtn" class="button theme-button">Continue</button>
            </form>
            <!-- <div class="text-center mt-3">
                                <p>or</p>
                            </div> -->
        </div>
        <?php

        require_once 'footer.php';

        ?>