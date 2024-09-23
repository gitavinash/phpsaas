<?php

$coupon_id = base64_decode(mysqli_real_escape_string($connection, $_GET['id']));

if ($couponDetails = mysqli_query($connection, "SELECT * FROM " . TABLE_PREFIX . "coupon WHERE coupon_id='{$coupon_id}' AND coupon_site_id=" . APP_SITE_ID)) {
    if (mysqli_num_rows($couponDetails) != 0) {
        extract(mysqli_fetch_assoc($couponDetails));
    } else {
        exit(); ?>
        <script type="text/javascript">
            alert('Invalid action found');
            window.location.href = 'dashboard/coupon/?type=list&status=all';
        </script>
<?php
    }
}

?>

<h2>Edit <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="box-1">
                <div class="form-group">
                    <label>Title*</label>
                    <input type="text" id="title" class="form-control inside-shadow" value="<?php echo $coupon_title ?>"
                        name="title" placeholder="Title*" autofocus required>
                </div>
            </div>
            <div class="box-1">
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="description"
                        placeholder="description"><?php echo $coupon_description ?></textarea>
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Code*</label>
                    <input type="text" id="code" class="form-control inside-shadow" value="<?php echo $coupon_code ?>"
                        name="code" placeholder="Code" required>
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Payment mode</label>
                    <select class="form-control inside-shadow" name="paymentMode" id="paymentMode" required>
                        <option value="#">Payment mode</option>
                        <option value="all" <?php echo (($coupon_payment_mode == 'all') ? 'selected' : '') ?>>All
                        </option>
                        <option value="cash" <?php echo (($coupon_payment_mode == 'cash') ? 'selected' : '') ?>>Cash
                        </option>
                        <option value="online" <?php echo (($coupon_payment_mode == 'online') ? 'selected' : '') ?>>
                            Online
                        </option>
                    </select>
                </div>
            </div>
            <section>
                <div class="box-2">
                    <div class="form-group">
                        <label>Discount</label>
                        <input type="number" onwheel="this.blur()" id="discount" class="form-control inside-shadow"
                            value="<?php echo $coupon_discount ?>" name="discount" placeholder="Discount" required>
                    </div>
                </div>
                <div class="box-2">
                    <div class="form-group">
                        <label>Discount type</label>
                        <select class="form-control inside-shadow" name="discountType" id="discountType" required>
                            <option value="#" <?php echo (($coupon_discount_type == '#') ? 'selected' : '') ?>>Dicount
                                type
                            </option>
                            <option value="%" <?php echo (($coupon_discount_type == '%') ? 'selected' : '') ?>>
                                Percentage (%)
                            </option>
                            <option value="₹" <?php echo (($coupon_discount_type == '₹') ? 'selected' : '') ?>>INR (₹)
                            </option>
                            <option value="$" <?php echo (($coupon_discount_type == '$') ? 'selected' : '') ?>>USD ($)
                            </option>
                        </select>
                    </div>
                </div>
            </section>
            <section>
                <div class="box-2">
                    <div class="form-group">
                        <label>Minimum cart amount</label>
                        <input type="number" onwheel="this.blur()" id="minCartAmount" value="<?php echo $coupon_min_value ?>"
                            class="form-control inside-shadow" name="minCartAmount" placeholder="Minimum cart amount">
                    </div>
                </div>
                <div class="box-2">
                    <div class="form-group">
                        <label>Maximum discount amount</label>
                        <input type="number" onwheel="this.blur()" id="maxDiscount" class="form-control inside-shadow"
                            value="<?php echo $coupon_max_discount ?>" name="maxDiscount"
                            placeholder="( Ignore for same as the discount amount )">
                    </div>
                </div>
            </section>
            <section>
                <div class="box-2">
                    <div class="form-group">
                        <label>Limit</label>
                        <input type="number" onwheel="this.blur()" id="limit" class="form-control inside-shadow"
                            value="<?php echo $coupon_limit ?>" name="limit" placeholder="0 for unlimited use">
                    </div>
                </div>
                <div class="box-2">
                    <div class="form-group">
                        <label>Expiry date*</label>
                        <input type="text" class="datepicker form-control inside-shadow"
                            value="<?php echo date('d/m/Y', strtotime($coupon_expire)) ?>" name="expiryDate"
                            placeholder="Ignore for lifetime">
                    </div>
                </div>
            </section>
        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="button" id="updateCoupon" class="btn btn-green btn-block" value="Edit coupon">
                <input type="hidden" name="coupon_id" value="<?php echo base64_encode($coupon_id) ?>">
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="update">
            </div>
        </div>
    </form>
</div>