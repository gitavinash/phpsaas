<h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
		<div class="left-box">
			<div class="box-1">
                <div class="form-group">
                    <label>Coupon code</label>
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" autofocus required >
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Coupon code</label>
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" required >
                </div>
            </div>
			<div class="box-2">
                <div class="form-group">
                    <label>Coupon code</label>
                    <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Title" required >
                </div>
            </div>
		</div>
		<div class="right-box">
			<div class="action-box inside-shadow">
				<input type="button" id="publishPost" class="btn btn-green btn-block" value="Add new coupon">
				<input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>" >
				<input type="hidden" name="action" value="publish" >
			</div>
		</div>
	</form>
</div>