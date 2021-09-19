<footer>
    <div class="foter-top">
        <div class="box-1">
            <h2>Quick links</h2>
            <ul>
                <li><a href="https://srisanwalimurat.com/">Home</a></li>
                <li><a href="https://srisanwalimurat.com/about-us/">About Us</a></li>
                <li><a href="https://srisanwalimurat.com/contact-us/">Contact Us</a></li>
            </ul>
        </div>
        <div class="box-1">
            <h2>Quick links</h2>
            <ul>
                <li><a href="https://srisanwalimurat.com/publication/">Publication</a></li>
                <li><a href="https://srisanwalimurat.com/gallery/">Gallery</a></li>
                <li><a href="https://srisanwalimurat.com/events/">Events</a></li>
                <li><a href="https://srisanwalimurat.com/discourses/">Discourses</a></li>
                <li><a href="https://srisanwalimurat.com/bhajan/">Bhajan/Aarti</a></li>
                <li><a href="https://pangat.srisanwalimurat.com/" style="color: #fff">Request For Pangat</a></li>
            </ul>
        </div>
        <div class="box-1">
            <h2>Information</h2>
            <ul>
                <li><a href="https://srisanwalimurat.com/privacy-policy/">Privacy Policy</a></li>
                <li><a href="https://srisanwalimurat.com/disclaimer/">Disclaimer</a></li>
                <li><a href="https://srisanwalimurat.com/refund-policy/">Refund Policy</a></li>
                <li><a href="https://srisanwalimurat.com/terms-of-use/">Terms of Use</a></li>
            </ul>
        </div>
        <div class="box-1">
            <h2>Contact us</h2>
            <ul>
                <li>Address : 36, Gali Number 2, Block 69B, Dori Walan, Karol Bagh, New Delhi, Delhi 110005, India
                </li>
                <li>info@srisanwalimurat.com</li>
            </ul>
        </div>

    </div>
</footer>

<div class="whatsapp">
    <a href="https://wa.me/919821005828?text=Hello" target="_blank"><img
            src="<?php echo SITE_URL ?>/lib/resource/ui/img/whatsapp.png" alt=""></a>
</div>
<?php include_once NOTIFICATION.'index.php' ?>

<script type="text/javascript" src="<?php echo JS_LIBRARY ?>jquery.min.js"></script>
<script src="<?php echo JS_LIBRARY ?>popper.min.js"></script>
<script src="<?php echo JS_LIBRARY ?>bootstrap/bootstrap.min.js"></script>
<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js"></script>


<script type="text/javascript">
var calenderStartMonth = <?php echo $month; ?>;
var calenderStartYear = <?php echo $year; ?>;
var calenderDateStartFrom = <?php echo $day; ?>;
var breakfastBookingAmount = <?php echo ((defined('BREAKFAST_SLOT_PRICE')) ? BREAKFAST_SLOT_PRICE : 1500 ) ?>;
var lunchBookingAmount = <?php echo ((defined('LUNCH_SLOT_PRICE')) ? LUNCH_SLOT_PRICE : 3100 ) ?>;
var dinnerBookingAmount = <?php echo ((defined('DINNER_SLOT_PRICE')) ? DINNER_SLOT_PRICE : 3100 ) ?>;
</script>

<script type="text/javascript" src="<?php echo USER_JS ?>script.min.js"></script>

<script type="text/javascript">
// Search box
$('.openSearchForm').click(function(e) {
    e.preventDefault();
    $('#target').hide();
    $('.openSearchFormBox').show();
    $('.search-bar').focus();
    console.log('sdgdsf');
});

$('.closeSearchFormBtn').click(function(e) {
    e.preventDefault();
    $('.show').show(0);
    $('.hide').hide(0);
    $('.openSearchFormBox').hide();
});
$('.show').click(function() {
    $('#target').show(200);
    $('.show').hide(0);
    $('.hide').show(0);
});
$('.hide').click(function() {
    $('#target').hide(500);
    $('.show').show(0);
    $('.hide').hide(0);
});
$('.toggle').click(function() {
    $('#target').toggle('slow');
});
</script>

</body>

</html>