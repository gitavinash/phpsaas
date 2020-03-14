<?php

	$constituencyId=mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0 ));
	if($categoryData=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."constituency WHERE constituency_id='{$constituencyId}'")) {
		if(mysqli_num_rows($categoryData)!=0) {
			extract(mysqli_fetch_assoc($categoryData));
		} else {
            $constituency_id='';
            $constituency_name='';
            $constituency_state='';
            $constituency_type='';
            $constituency_year='';
		}
	}

?>
<h2>Manage <?php echo ucwords($postType); ?> <?php echo ((isset($_GET['id'])) ? "<span><a href='constituency/?type=list' class='btn btn-blue btn-sm' >Add New </a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="formData" >
				<h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New ' ).ucwords($postType); ?> </h3>
				<div class="form-group">
					<label>Constituency Name*</label>
					<input type="text" name="name" class="form-control inside-shadow" placeholder="Constituency name..." value="<?php echo $constituency_name; ?>" autofocus required >
				</div>
				<div class="form-group">
					<label>Constituency State*</label>
					<select name="state" class="form-control inside-shadow" required >
						<option value="">Select State</option>
						<option value="andaman_&_nicobar_islands">Andaman &amp; Nicobar Islands</option>
						<option value="andhra_pradesh">Andhra Pradesh</option>
						<option value="arunachal_pradesh">Arunachal Pradesh</option>
						<option value="assam">Assam</option>
						<option value="bihar">Bihar</option>
						<option value="chandigarh">Chandigarh</option>
						<option value="chattisgarh">Chattisgarh</option>
						<option value="dadra_&_nagar_haveli">Dadra &amp; Nagar Haveli</option>
						<option value="daman_&_diu">Daman &amp; Diu</option>
						<option value="goa">Goa</option>
						<option value="gujarat">Gujarat</option>
						<option value="haryana">Haryana</option>
						<option value="himachal_pradesh">Himachal Pradesh</option>
						<option value="jammu_&_kashmir">Jammu &amp; Kashmir</option>
						<option value="jharkhand">Jharkhand</option>
						<option value="karnataka">Karnataka</option>
						<option value="kerala">Kerala</option>
						<option value="lakshadweep">Lakshadweep</option>
						<option value="madhya_pradesh">Madhya Pradesh</option>
						<option value="maharashtra">Maharashtra</option>
						<option value="manipur">Manipur</option>
						<option value="meghalaya">Meghalaya</option>
						<option value="mizoram">Mizoram</option>
						<option value="nagaland">Nagaland</option>
						<option value="delhi" selected >NCT OF Delhi</option>
						<option value="odisha">Odisha</option>
						<option value="puducherry">Puducherry</option>
						<option value="punjab">Punjab</option>
						<option value="rajasthan">Rajasthan</option>
						<option value="sikkim">Sikkim</option>
						<option value="tamil_nadu">Tamil Nadu</option>
						<option value="telangana">Telangana</option>
						<option value="tripura">Tripura</option>
						<option value="uttar_pradesh">Uttar Pradesh</option>
						<option value="uttarakhand">Uttarakhand</option>
						<option value="west_bengal">West Bengal</option>
					</select>
				</div>
				<div class="form-group">
					<label>Constituency Type*</label>
					<select name="category" class="form-control inside-shadow" required >
						<option value="">Constituency type</option>
						<option value="lok_sabha">Lok Sabha</option>
						<option value="vidhan_sabha" selected >Vidhan Sabha</option>
					</select>
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="<?php echo ((isset($_GET['id'])) ? 'update' : 'publish' ) ?>">
					<input type="hidden" name="action" value="<?php echo $postType; ?>">
					<input type="hidden" name="constituency_id" value="<?php echo $constituency_id; ?>">
					<input type="submit" id="submitForm" class="btn btn-green" value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add New' ); ?> Constituency" >
				</div>
			</form>
		</div>
		<div class="category-list">
			<h3><?php echo ucwords($postType); ?> List</h3>
			<table id="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>State</th>
						<th>Type</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."constituency ORDER BY constituency_name ASC")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="constituency/?type=list&id=<?php echo $constituency_id; ?>" ><?php echo $constituency_name ?></a></p>
											<div class="hover">
												<a href="constituency.php?type=list&id=<?php echo $constituency_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<a href="delete.php?type=constituency&id=<?php echo $constituency_id; ?>"><i class="fas fa-trash"></i> Delete</a>
											</div>
										</td>
										<td><?php echo ucwords(str_replace('_', ' ', $constituency_state)); ?></td>
										<td><?php echo ucwords(str_replace('_', ' ', $constituency_type)); ?></td>
									</tr>
								<?php
								}
							}
						}

					?>
				</tbody>
			</table>
		</div>
	</div>
</div>