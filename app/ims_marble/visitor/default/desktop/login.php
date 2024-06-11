<?php

    $redirect_url=((isset($redirect_url)) ? $redirect_url : '/dashboard/' );

    if(isset($_COOKIE['isLogin'])) {
        
        header("Location: {$redirect_url}");
        exit();

    }

    if(SITE_VERSION=='desktop') {
        $context = stream_context_create([
            'http' => [
                'timeout' => 60
            ]
        ]);

        $quoteOfTheDay = file_get_contents("https://api.forismatic.com/api/1.0/?method=getQuote&format=json&key=1&lang=en", false, $context);
        $quoteOfTheDay = json_decode($quoteOfTheDay, TRUE);
    } else {
        $$quoteOfTheDay['quoteText']='';
        $quoteOfTheDay['quoteAuthor']='';
    }
    
    require_once 'header.php';

?>
<div class="col d-none d-lg-block">
    <div class="slider">
        <div class="slide">
            <div class="blockquote-wrapper">
                <div class="blockquote">
                    <p><?php echo $quoteOfTheDay['quoteText'] ?></p>
                    <p><strong>&mdash; <?php echo $quoteOfTheDay['quoteAuthor'] ?></strong></p>
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
                <strong>Welcome Back!</strong> Log In to Your Account
            </div>
            <form id="login" autocomplete="off">
                <div class="form-group">
                    <input type="text" class="form-control input-with-label" name="email" id="email"
                        placeholder="Email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control input-with-label" name="pass" id="pass"
                        placeholder="Password">
                </div>
                <div class="form-group">
                    <p>Forgot your password? <a href="/forgot-password/">Click Here</a></p>
                </div>
                <input type="hidden" name="type" value="auth">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>">
                <button type="submit" id="loginBtn" class="button theme-button">Log in</button>
                <div class="form-group">
                    <p>Don't have an account yet? <a href="/register/">Register Now</a></p>
                </div>
            </form>
            <!-- <div class="text-center mt-3">
                                <p>or</p>
                            </div> -->
        </div>
        <?php

    require_once 'footer.php';

?>