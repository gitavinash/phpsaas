// Add the public key generated from the console here.
messaging.usePublicVapidKey("BDqd94hGAtdU3-XdG3MtNtB8VTg1cjv8AOtHtAIxyfdPALJ9hHmjBJnfB8ZExRc-T5EFiFceYUrMztiwPZINOBY");

navigator.serviceWorker.register('/lib/resource/notification/firebase-messaging-sw.js').then((registration) => {

    messaging.useServiceWorker(registration);

    messaging.getToken().then((currentToken) => {
        
        if (currentToken) {

            /** Store Browser Id into Database */
            
            saveToken(currentToken);
            sendTokenToServer(currentToken);

        } else {
    
            console.log('No Instance ID token available. Request permission to generate one.');
            setTokenSentToServer(false);

        }

    }).catch((err) => {

        console.log('An error occurred while retrieving token. ', err);
        setTokenSentToServer(false);

    });

    messaging.onTokenRefresh(() => {
        
        messaging.getToken().then((refreshedToken) => {

            console.log('Refreshed Token : ' + refreshedToken);

            setTokenSentToServer(false);
            sendTokenToServer(refreshedToken);

        }).catch((err) => {

            console.log('Unable to retrieve refreshed token ', err);
            
        });

    });

    function sendTokenToServer(currentToken) {
        
        if (!isTokenSentToServer()) {

            setTokenSentToServer(true);

        }

    }

    function isTokenSentToServer() {
        
        return window.localStorage.getItem('sentToServer') === '1';

    }

    function setTokenSentToServer(sent) {
        
        window.localStorage.setItem('sentToServer', sent ? '1' : '0');

    }

    function saveToken(currentToken) {

        $.ajax({
            url: '/api/notification/register.php',
            type: 'GET',
            data: {
                'device' : deviceType,
                'token'  : currentToken
            },
            success: function(data) {
                console.log(data.msg);
            },
            error: function(data) {
                console.log(data);
            }
        });

    }

    messaging.onMessage((payload) => {
        
        notificationTitle = payload.notification.title;
        notificationOption = {
            body : payload.notification.body,
            icon : payload.notification.icon,
        }
        
        var notification = new Notification(notificationTitle, notificationOption);

    });

});