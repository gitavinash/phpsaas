// notification dropdown
var notification = document.querySelector('.notification');
var dropdown = document.querySelector('.notification_container');

notification.addEventListener('click', () => {
    dropdown.classList.toggle('active');
});

// itinerary show
var itinerary = document.querySelector('.itinerary');
var itineraryContent = document.querySelector('.itinerary_wrapper');
var cancel = document.querySelector('.cancel_itinerary');

itinerary.addEventListener('click', () => {
    itineraryContent.classList.toggle('show');
});
cancel.addEventListener('click', () => {
    itineraryContent.classList.toggle('show');
});