<h2>Add New Lab</h2>

<div class="form-box">

    <form id="addNewBuilding">
        <div class="box-2">
            <div class="form-group">
                <label>Lab Name</label>
                <input type="text" class="form-control inside-shadow" name="name" placeholder="Lab Name" >
            </div>
            <div class="form-group">
                <label>Lab Address</label>
                <input type="text" class="form-control inside-shadow" name="name" placeholder="Lab Name" >
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country" class="form-control inside-shadow">
                    <option value="">Select Country</option>
                    <option value="" selected >India</option>
                </select>
            </div>
            <div class="form-group">
                <label>State</label>
                <select name="state" class="form-control inside-shadow">
                    <option value="">Select State</option>
                    <option value="">Delhi</option>
                    <option value="">Mumbai</option>
                    <option value="">Kolkata</option>
                    <option value="">Vijayawada</option>
                    <option value="">Bengalore</option>
                </select>
            </div>
            <div class="form-group">
                <label>City</label>
                <select name="state" class="form-control inside-shadow">
                    <option value="">Select City</option>
                    <option value="">City Name 1</option>
                    <option value="">City Name 2</option>
                    <option value="">City Name 3</option>
                    <option value="">City Name 4</option>
                    <option value="">City Name 5</option>
                    <option value="">City Name 6</option>
                    <option value="">City Name 7</option>
                    <option value="">City Name 8</option>
                    <option value="">City Name 9</option>
                    <option value="">City Name 10</option>
                </select>
            </div>
            <div class="form-group">
				<input type="hidden" name="type" value="clinic" >
				<input type="hidden" name="action" value="add" >
				<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
				<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
                <input type="submit" id="updatePasswordBtn" value='Add Clinic' class='btn btn-green'>
            </div>
        </div>
    </form>

</div>