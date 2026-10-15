// function toggleVideo() {
//     var video = document.querySelector(".myVideo");
//     var button = document.querySelector(".play_icon");
//     var icon = document.getElementById("playIcon");
  
//     if (video.paused) {
//       video.play();
//       icon.classList.remove("bx-play");
//         icon.classList.add("bx-pause");
//         // video.removeAttribute("controls");
//     } else {
//       video.pause();
//       icon.classList.remove("bx-pause");
//         icon.classList.add("bx-play");
//         // video.setAttribute("controls", "controls");
//     }
//   }
  

var play = document.querySelector('.play_icon');
var ytVideo = document.querySelector('.yotube_video');
var ytCancel = document.querySelector('.video_cancel');

play.addEventListener('click', () => {
  ytVideo.classList.toggle('active');
});
ytCancel.addEventListener('click', () => {
  ytVideo.classList.toggle('active');
});

