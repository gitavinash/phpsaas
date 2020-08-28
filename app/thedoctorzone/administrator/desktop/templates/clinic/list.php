<h2 style="margin-bottom: 20px" >All Clinc and Hospitals</h2>
<!-- <div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $postType ?></option>
				<option value="206" <?php echo (($_GET['status']=='206') ? 'selected' : '' ) ?> >Drafts <?php echo $postType ?></option>
				<option value="200" <?php echo (($_GET['status']=='200') ? 'selected' : '' ) ?>  >Published <?php echo $postType ?></option>
				<option value="201" <?php echo (($_GET['status']=='201') ? 'selected' : '' ) ?> >Repoters <?php echo $postType ?></option>
				<option value="202" <?php echo (($_GET['status']=='202') ? 'selected' : '' ) ?>  >Interns <?php echo $postType ?></option>
			</select>
			<select name="category" class="form-control">
				<option value="all">Select Category</option>
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title FROM ".TABLE_PREFIX."category WHERE category_site_id='".SITE_ID."' AND category_type='{$postType}'")) {
						while($category=mysqli_fetch_assoc($categoryList)) { ?>
							<option value="<?php echo $category['category_id']; ?>" <?php echo (($category['category_id']==$categoryID) ? 'selected' : '' ) ?>  > <?php echo ucwords($category['category_title']); ?></option>
						<?php
						}
					}
				?>
			</select>
			<select name="author" class="form-control">
				<option value="all">Select Author</option>
				<?php
					if($authorList=mysqli_query($connection, "SELECT profile_owner_id, profile_display_name FROM ".TABLE_PREFIX."profile, ".TABLE_PREFIX."user WHERE user_id=profile_owner_id AND user_status=1 ORDER BY profile_display_name ASC")) {
						while($author=mysqli_fetch_assoc($authorList)) { ?>
							<option value="<?php echo $author['profile_owner_id']; ?>" <?php echo (($author['profile_owner_id']==$authorID) ? 'selected' : '' ) ?>  > <?php echo ucwords($author['profile_display_name']); ?></option>
						<?php
						}
					}
				?>
			</select>
			<input type="submit" class="btn" value="Filter" >
		</form>
	</div>
	<div class="right-box">
		
	</div>
</div> -->
<table id="table">
	<thead>
		<tr>
			<th>S No.</th>
			<th>Clinic / Hospital Name</th>
			<th>Address</th>
			<th style="width: 70px">Pin Code</th>
			<th style="width: 100px"></th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($clincInfo=mysqli_query($connection, "SELECT company_id, company_title, address_house_no, address_area, address_landmark, address_pin_code, city_name, state_name, country_name FROM ".TABLE_PREFIX."company INNER JOIN ".TABLE_PREFIX."address ON address_id=company_address_id INNER JOIN ".TABLE_PREFIX."city ON city_id=address_city_id INNER JOIN ".TABLE_PREFIX."state ON address_state_id=state_id INNER JOIN ".TABLE_PREFIX."country ON country_id=address_country_id WHERE company_type='clinic' AND company_status=1")) {

				if(mysqli_num_rows($clincInfo)!=0) {

					$i=1;

					while($clincData=mysqli_fetch_assoc($clincInfo)) {

						extract($clincData);

						echo "<tr>
							<td>{$i}</td>
							<td>{$company_title}</td>
							<td>{$address_house_no}, {$address_area}, {$address_landmark}, {$city_name}, {$state_name}, {$country_name}</td>
							<td>{$address_pin_code}</td>
							<td>
								<a href='clinic/?type=edit&id={$company_id}' class='btn btn-blue btn-sm' >Edit</a>
								<a href='clinic/?type=delete&id={$company_id}' class='btn btn-red btn-sm' >Delete</a>
							</td>
						</tr>";

						$i++;

					}

				} else {

					echo "<tr><td colspan='5'>No Information Found</td></tr>";

				}

			} else {

				echo mysqli_error($connection);

			}

		?>
	</ul>
</div>