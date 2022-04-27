<footer>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <img src="<?php echo USER_IMAGES ?>logo.png" class="footer_logo">
                        <p>J-215, Saket, New Delhi-17</p>
                        <p>Phone: +91 92665 55500</p>
                        <p>Email: info@skinqure.in</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="footer_heading">Quick Links</h3>

                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="/about-us/">About Us</a></li>
                            <li><a href="/blogs/">Blogs</a></li>
                            <li><a href="/contact-us/">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h3 class="footer_heading">About Team</h3>

                        <ul>
                            <li><a href="#">Our Dermatologists</a></li>
                            <li><a href="#">Aestheticians</a></li>
                            <li><a href="#">Managers</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h3 class="footer_heading">Services</h3>
                        <ul>
                            <li><a href="">Laser Hair reduction</a></li>
                            <li><a href="">Laser for acne scars</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
<div class="container-fluid copyright">
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <p>Copyright @ <?php echo date('Y') ?>. All Rights Reserved</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js'></script>
<script type="text/javascript">
var divisor = document.getElementById("divisor"),
    handle = document.getElementById("handle"),
    slider = document.getElementById("slider");

function moveDivisor() {
    handle.style.left = slider.value + "%";
    divisor.style.width = slider.value + "%";
}

window.onload = function() {
    moveDivisor();
};
</script>
<script>
// Swiper Configuration
var swiper = new Swiper(".swiper-container", {
    slidesPerView: 3.5,
    spaceBetween: 30,
    centeredSlides: true,
    freeMode: true,
    grabCursor: true,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true
    },
    autoplay: {
        delay: 4000,
        disableOnInteraction: false
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev"
    },
    breakpoints: {
        500: {
            slidesPerView: 1
        },
        700: {
            slidesPerView: 3.5
        }
    }
});
</script>
<script>
$('.counting').each(function() {
    var $this = $(this),
        countTo = $this.attr('data-count');

    $({
        countNum: $this.text()
    }).animate({
            countNum: countTo
        },

        {

            duration: 3000,
            easing: 'linear',
            step: function() {
                $this.text(Math.floor(this.countNum));
            },
            complete: function() {
                $this.text(this.countNum);
                //alert('finished');
            }

        });


});
</script>
<script>
document.addEventListener('contextmenu', event => event.preventDefault());
</script>
<script>
document.onkeydown = function(e) {
    if (e.ctrlKey &&
        (e.keyCode === 67 ||
            e.keyCode === 86 ||
            e.keyCode === 85 ||
            e.keyCode === 117)) {
        return false;
    } else {
        return true;
    }
};
$(document).keypress("u", function(e) {
    if (e.ctrlKey) {
        return false;
    } else {
        return true;
    }
});
</script>
</body>

</html>