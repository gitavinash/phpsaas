// Add the public key generated from the console here.
messaging.usePublicVapidKey("BDqd94hGAtdU3-XdG3MtNtB8VTg1cjv8AOtHtAIxyfdPALJ9hHmjBJnfB8ZExRc-T5EFiFceYUrMztiwPZINOBY");

navigator.serviceWorker.register('/lib/resource/notification/firebase-messaging-sw.js').then((registration) => {

    messaging.useServiceWorker(registration);

    messaging.getToken().then((currentToken) => {
    
        if (currentToken) {

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

            console.log('Token refreshed.');
            setTokenSentToServer(false);
            sendTokenToServer(refreshedToken);

        }).catch((err) => {

            console.log('Unable to retrieve refreshed token ', err);
            
        });
    });

    function sendTokenToServer(currentToken) {

        if (!isTokenSentToServer()) {

            console.log('Sending token to server...');
            setTokenSentToServer(true);

            /** Store Browser Id into Database */

        }

    }

    function isTokenSentToServer() {
        
        return window.localStorage.getItem('sentToServer') === '1';

    }

    function setTokenSentToServer(sent) {
    
        window.localStorage.setItem('sentToServer', sent ? '1' : '0');

    }

});