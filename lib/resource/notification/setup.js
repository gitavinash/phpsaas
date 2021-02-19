console.log('%c Developed by ITS Soft World', 'color : #01ABDF; font-weight : bold; font-size: 18px');
console.log('%c see www.itssoftworld.com for more information', 'color: #333; font-weight: bold; font-size: 14px');

// Add the public key generated from the console here.
// messaging.usePublicVapidKey("BOYSp7aQU2DXGAuYOk4FlA_SgE8u7j6T1nF9kDkG1RQNGYKwDYljyg9t-Eme7kkLdD2mMKYpt254wIkiLEK7cEQ");

// navigator.serviceWorker.register('/service-worker.js').then((registration) => {

//     messaging.useServiceWorker(registration);

//     messaging.getToken().then((currentToken) => {
        
//         if (currentToken) {

//             /** Store Browser Id into Database */
            
//             saveToken(currentToken);
//             sendTokenToServer(currentToken);

//         } else {
    
//             console.log('No Instance ID token available. Request permission to generate one.');
//             setTokenSentToServer(false);

//         }

//     }).catch((err) => {

//         console.log('An error occurred while retrieving token. ', err);
//         setTokenSentToServer(false);

//     });

//     messaging.onTokenRefresh(() => {
        
//         messaging.getToken().then((refreshedToken) => {

//             console.log('Refreshed Token : ' + refreshedToken);

//             setTokenSentToServer(false);
//             sendTokenToServer(refreshedToken);

//         }).catch((err) => {

//             console.log('Unable to retrieve refreshed token ', err);
            
//         });

//     });

//     function sendTokenToServer(currentToken) {
        
//         if (!isTokenSentToServer()) {

//             setTokenSentToServer(true);

//         }

//     }

//     function isTokenSentToServer() {
        
//         return window.localStorage.getItem('sentToServer') === '1';

//     }

//     function setTokenSentToServer(sent) {
        
//         window.localStorage.setItem('sentToServer', sent ? '1' : '0');

//     }

//     function saveToken(currentToken) {

//         $.ajax({
//             url: '/api/notification/register.php',
//             type: 'GET',
//             data: {
//                 'device' : deviceType,
//                 'token'  : currentToken
//             },
//             success: function(data) {
//                 console.log(data.msg);
//             },
//             error: function(data) {
//                 console.log(data);
//             }
//         });

//     }

//     messaging.onMessage((payload) => {
        
//         notificationTitle = payload.notification.title;
//         notificationOption = {
//             body : payload.notification.body,
//             icon : payload.notification.icon,
//         }
        
//         var notification = new Notification(notificationTitle, notificationOption);

//     });

// });

/** Fetch device location */

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        msg = "Geolocation is not supported by this browser.";
    }
}
      
function showPosition(position) {

    longitude = position.coords.longitude;
    latitude = position.coords.latitude;

}