<?php
        
    require_once 'header.php';

?>
<section class="search_wrapper">
    <div class="container">
        <h3>search</h3>
        <div class="search_input">
            <input type="text" placeholder="What are you looking for?" name="" id="" autofocus>
            <button class="search_icon">
                <i class='bx bx-search'></i>
            </button>
        </div>
    </div>
</section>

<!-- cart page -->
<section class="cart_wrapper">
    <div class="cart_content">
        <div class="cart_header">
            <h6>your cart</h6>
            <div class="cart_cancel">
                <i class='bx bx-x'></i>
            </div>
        </div>
        <div class="cart_container">
            <div class="cart_flex">
                <div class="cart_container__left">
                    <div class="cart_img">
                        <img src="assets/images/img1.jpg" class="img-fluid" alt="">
                    </div>

                </div>
                <div class="cart_container__right">
                    <div class="cart_row">
                        <div class="cart_product__name">
                            <h6>Merino Wool Polka Dot Scarf - Denim Blue</h6>
                        </div>
                        <div class="remove_item">
                            <button><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <div class="cart_row">
                        <div class="cart_quentity">
                            <p id="minus-btn">-</p>
                            <p id="count">1</p>
                            <p id="plus-btn">+</p>
                        </div>
                        <div class="cart_price">
                            <p>₹599</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="cart_flex">
                <div class="cart_container__left">
                    <div class="cart_img">
                        <img src="assets/images/img2.jpg" class="img-fluid" alt="">
                    </div>

                </div>
                <div class="cart_container__right">
                    <div class="cart_row">
                        <div class="cart_product__name">
                            <h6>Merino Wool Polka Dot Scarf - Denim Blue</h6>
                        </div>
                        <div class="remove_item">
                            <button><i class='bx bx-trash'></i></button>
                        </div>
                    </div>
                    <div class="cart_row">
                        <div class="cart_quentity">
                            <p id="minus-btn2">-</p>
                            <p id="count2">1</p>
                            <p id="plus-btn2">+</p>
                        </div>
                        <div class="cart_price">
                            <p>₹599</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="total_container">
                <div class="total_flex">
                    <h6>subtotal:</h6>
                    <p>₹599</p>
                </div>
                <div class="total_flex">
                    <h6>total:</h6>
                    <p><strong>₹599</strong></p>
                </div>
                <div class="checkout">
                    <a href="checkout.html">
                        <button>checkout</button>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
<?php
        
    require_once 'footer.php';

?>