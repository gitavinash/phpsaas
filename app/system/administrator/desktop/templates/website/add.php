<h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

<div class="form-box">
	<form method="POST" id="formData" enctype="multipart/form-data" >
        <div class="box-2">
            <div class="form-group">
                <label>Website Name*</label>
                <input type="text" name="name" class='form-control inside-shadow' placeholder='Website Name ( Like :- ITS Soft World, Promod Groups )' autofocus required >
            </div>
            <div class="form-group">
                <label>Website Username*</label>
                <input type="text" name="username" id="checkUsername" class="form-control inside-shadow" placeholder="Website Username ( Like :- itssoftworld, avinashkumar )" >
            </div>
            <div class="form-group">
                <label>Website Domain</label>
                <input type="text" name="domain" class='form-control inside-shadow' placeholder='Website Domain ( Like :- itssoftworld.com, avinashkumar.in )' required >
            </div>
            <div class="form-group">
                <label>Contact Person First Name*</label>
                <input type="text" name="first_name" class='form-control inside-shadow' placeholder='Contact Person Name ( Like :- Avinash, Amit, Rishi )' required >
            </div>
            <div class="form-group">
                <label>Contact Person Last Name*</label>
                <input type="text" name="last_name" class='form-control inside-shadow' placeholder='Contact Person Name ( Like :- Kumar, Sharma, Mishra )' required >
            </div>
            <div class="form-group">
                <label>Email*</label>
                <input type="email" name='email' class='form-control inside-shadow' placeholder='Email ( Like :- example@gmail.com, abc@yahoo.com )' required >
            </div>
            <div class="form-group">
                <label>Mobile No.*</label>
                <input type="number" name='mobile' class='form-control inside-shadow' placeholder='Mobile No ( Like :- 9990026008, 9990026009 )' >
            </div>
            <div class="form-group">
                <label>Website Type*</label>
                <select name='site_type' class='form-control inside-shadow' required>
                    <option value="">Select Website Type</option>
                    <option value="cms">CMS ( Content Management System )</option>
                    <option value="spa">SMS ( Spa Management System )</option>
                    <option value="payroll">PMS ( Payroll Management System )</option>
                    <option value="lead">LMS ( Lead Management System )</option>
                    <option value="news">NMS ( News Management System )</option>
                </select>
            </div>
            <div class="form-group">
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
				<input type="hidden" name="type" value="website" >
				<input type="hidden" name="action" value="add" >
                <input type="submit" id="addWebsite" class="btn btn-green submitBtn" value="Create New Website">
            </div>
        </div>
	</form>
</div>