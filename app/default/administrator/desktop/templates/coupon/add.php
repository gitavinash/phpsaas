<h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="box-1">
                <div class="form-group">
                    <label>Title*</label>
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title*" autofocus required >
                </div>
            </div>
			<div class="box-1">
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control inside-shadow" name="description" placeholder="description" ></textarea>
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Code*</label>
                    <input type="text" id="code" class="form-control inside-shadow" name="code" placeholder="Code" required >
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Payment mode</label>
                    <select class="form-control inside-shadow" name="paymentMode" id="paymentMode" required >
                        <option value="#">Payment mode</option>
                        <option value="all" selected >All</option>
                        <option value="cash" >Cash</option>
                        <option value="online" >Online</option>
                    </select>
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Discount</label>
                    <input type="number" id="discount" class="form-control inside-shadow" name="discount" placeholder="Discount" required >
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Discount type</label>
                    <select class="form-control inside-shadow" name="discountType" id="discountType" required >
                        <option value="#" >Dicount type</option>
                        <option value="%" selected >Percentage (%)</option>
                        <option value="₹">INR (₹)</option>
                        <option value="$">USD ($)</option>
                    </select>
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Minimum cart amount</label>
                    <input type="number" id="minCartAmount" class="form-control inside-shadow" name="minCartAmount" placeholder="Minimum cart amount" >
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Maximum discount amount</label>
                    <input type="number" id="maxDiscount" class="form-control inside-shadow" name="maxDiscount" placeholder="( Ignore for same as the discount amount )" >
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Limit</label>
                    <input type="number" id="limit" class="form-control inside-shadow" name="limit" placeholder="0 for unlimited use" >
                </div>
            </div>
            <div class="box-2">
                <div class="form-group">
                    <label>Expiry date*</label>
                    <input type="text" class="datepicker form-control inside-shadow" name="expiryDate" placeholder="Ignore for lifetime" >
                </div>
            </div>
		</div>
		<div class="right-box">
			<div class="action-box inside-shadow">
				<input type="button" id="addNewCoupon" class="btn btn-green btn-block" value="Add new coupon">
				<input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<input type="hidden" name="action" value="publish" >
			</div>
		</div>
	</form>
</div>