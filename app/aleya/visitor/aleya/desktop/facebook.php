<?php

    if(!isset($token)){
        echo "Something went wrong";
        exit();
    }
    if(!isset($site)){
        echo "Something went wrong";
        exit();
    }
    if(!isset($new)){
        $new='false';
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continue with Facebook</title>
    <link rel="icon" href="<?php echo USER_IMAGES ?>aleya-icon.png" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo USER_IMAGES ?>aleya-icon.png" type="image/x-icon">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        flex-direction: column;
        background-color: #f4f4f4;
    }

    a {
        text-decoration: none;
    }

    .container {
        max-width: 600px;
        text-align: center;
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .partnership {
        margin-bottom: 30px;
    }

    .partnership img {
        width: auto;
        height: auto;
        max-height: 70px;
        border: 1px solid #01647B;
        border-radius: 10px;
        margin-left: 20px;
    }

    .arrow {
        width: 50px;
        height: auto;
    }

    .content {
        margin-bottom: 30px;
    }

    .continue-btn {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #4267B2;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .footer {
        margin-top: 30px;
        font-size: 14px;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="partnership">
            <img src="<?php echo USER_IMAGES ?>facebook-icon.png" alt="Facebook Logo">
            <img src="<?php echo USER_IMAGES ?>aleya-icon.png" alt="Aleya Logo">
        </div>
        <div class="content">
            <p>Click the "Continue to Facebook" button below to start accessing your Facebook Leads data in Aleya.</p>
        </div>
        <button class="continue-btn" onclick="loginWithFacebook()">Continue to Facebook</button>
        <div class="footer">
            <a href="https://blumox.com/privacy-policy/" target="_blank">Privacy Policy</a> | <a
                href="https://blumox.com/terms-and-conditions/" target="_blank">Terms and
                Conditions</a>
        </div>
        <script>
        // Initialize the Facebook SDK
        window.fbAsyncInit = function() {
            FB.init({
                appId: '927866918819525',
                cookie: true,
                xfbml: true,
                version: 'v19.0'
            });

            // Check login status
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        };

        // Load the Facebook SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Function to handle status change
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your webpage and Facebook.
                console.log('Welcome! Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log(response);
                    console.log('Successful login for: ' + response.name);
                    // Here you can do something with the user's information
                });
            } else {
                // Not logged into your webpage or we are unable to tell.
                console.log('Please log into this webpage and Facebook.');
            }
        }

        // Function to handle login button click
        function loginWithFacebook() {
            FB.login(function(response) {
                console.log(`loginWithFacebook : `, response);
                statusChangeCallback(response);

                if (response.status === 'connected') {

                    var accessToken = response.authResponse.accessToken;
                    var accessExpiry = response.authResponse.data_access_expiration_time;
                    var accessUserId = response.authResponse.userID;

                    var url =
                        '/api/aleya/integration/facebook/?token=<?php echo $token ?>&site=<?php echo $site ?>&new=<?php echo $new ?>&accessToken=' +
                        accessToken + '&accessExpiry' + accessExpiry + '&accessUserId=' +
                        accessUserId; // Replace with your endpoint URL

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json(); // Assuming response is JSON
                            window.history.back()
                        })
                        .then(data => {
                            console.log(data); // Process the response data
                        })
                        .catch(error => {
                            console.error('Fetch request failed:', error);
                        });

                }
            }, {
                // scope: '933906698051792'
                scope: "leads_retrieval,pages_manage_ads,pages_read_engagement,pages_show_list,pages_read_user_content,pages_manage_metadata,business_management,ads_management"
            }); // Add any additional permissions you need
        }
        </script>
    </div>

</body>

</html>