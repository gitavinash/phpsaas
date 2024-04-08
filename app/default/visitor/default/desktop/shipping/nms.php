<div class="row">
    <div class="box white-bg">
        <div class="col">
            <h2>Order summary</h2>
        </div>
        <div class="col">
            <?php
                $i=0;
                $subTotal = 0;
                while($cartData = mysqli_fetch_assoc($cartInfo)) {
                    extract($cartData);
                    if($i==0) {
                        echo "<div class='productBox'>
                            <div class='productImage col-2 no-padding'>
                                ".((${$cart_relation_type.'_image'}!='')  ? "<img src='{${$cart_relation_type.'_image'}}' alt=''>" : "<img src='".IMG_LIBRARY."profile_icon/".strtolower(((trim(${$cart_relation_type.'_title'}[0])!='')?
            ${$cart_relation_type.'_title'}[0] : 'unknown')).".png' alt='' srcset=''>" )."
        </div>
        <div class='productText col-8'>
            <!-- <div class='row'> -->
            <div class='col-8'>
                <p><strong>{${$cart_relation_type.'_title'}}</strong></p>
                <p><span>{${$cart_relation_type.'_description'}}</span></p>
            </div>
            <div class='col-2'>
                <div class='price float-right'>
                    <p>{$inventory_currency_symbol} {$inventory_price}</p>
                </div>
            </div>
            <!-- </div> -->
        </div>
    </div>";
    $subTotal=number_format((float)$subTotal + $inventory_price, 2, '.', '');
    } else {
    continue;
    }
    $i++;
    }

    ?>
        </div>
    </div>
</div>