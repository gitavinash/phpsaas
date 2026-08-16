<div class="box white-bg checkout-card">
    <div class="card-title-bar">
        <h2>
            <svg class="title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <span>Order summary</span>
        </h2>
    </div>
    <div class="product-list">
        <?php
        $subTotal = 0;
        $itemCount = 0;
        if (isset($cartInfo) && $cartInfo) {
            while ($cartData = mysqli_fetch_assoc($cartInfo)) {
                extract($cartData);
                $itemCount++;

                $titleVar = $cart_relation_type . '_title';
                $descVar = $cart_relation_type . '_description';
                $imageVar = $cart_relation_type . '_image';

                $itemTitle = isset($$titleVar) ? $$titleVar : 'Item';
                $itemDesc = isset($$descVar) ? $$descVar : '';
                $itemImage = (isset($$imageVar) && !empty($$imageVar)) ? $$imageVar : '';

                $firstChar = (!empty($itemTitle) && isset($itemTitle[0])) ? strtolower(trim($itemTitle[0])) : 'unknown';
                $fallbackImage = IMG_LIBRARY . "profile_icon/" . $firstChar . ".png";
                $imgSrc = !empty($itemImage) ? $itemImage : $fallbackImage;

                $itemPrice = isset($inventory_price) ? (float)$inventory_price : 0;
                $currencySymbol = isset($inventory_currency_symbol) ? $inventory_currency_symbol : '₹';
                $subTotal += $itemPrice;
                $typeLabel = str_replace('_', ' ', strtoupper($cart_relation_type));

        ?>
                <div class="productBox">
                    <div class="productImage">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($itemTitle); ?>" onerror="this.src='<?php echo $fallbackImage; ?>';">
                    </div>
                    <div class="productText">
                        <div class="productDetails">
                            <p class="productTitle"><strong><?php echo htmlspecialchars($itemTitle); ?></strong></p>
                            <?php if (!isset($order) && !empty($itemDesc)): ?>
                                <p class="productDesc"><span><?php echo $itemDesc; ?></span></p>
                            <?php endif; ?>
                        </div>
                        <div class="price">
                            <p class="priceTag"><?php echo $currencySymbol . ' ' . number_format($itemPrice, 2); ?></p>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>