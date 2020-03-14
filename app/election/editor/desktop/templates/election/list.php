<?php

	$electionId=mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0 ));
	if($categoryData=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."election WHERE election_id='{$electionId}'")) {
		if(mysqli_num_rows($categoryData)!=0) {
			extract(mysqli_fetch_assoc($categoryData));
		} else {
            $election_id='';
            $election_name='';
            $election_state='';
            $election_party_1='';
            $election_party_2='';
            $election_party_3='';
            $election_party_4='';
            $election_party_5='';
            $election_total_seats='';
            $election_type='';
            $election_result_date='';
            $election_date='';
            $election_year='';
		}
	}

?>
<h2>Manage <?php echo ucwords($postType); ?> <?php echo ((isset($_GET['id'])) ? "<span><a href='election/?type=list' class='btn btn-blue btn-sm' >Add New </a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="formData" >
				<h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New ' ).ucwords($postType); ?> </h3>
				<div class="form-group">
					<label>Election Name*</label>
					<input type="text" name="name" class="form-control inside-shadow" placeholder="Election name..." value="<?php echo $election_name; ?>" autofocus required >
				</div>
				<div class="form-group">
					<label>Election State*</label>
					<select name="state" class="form-control inside-shadow" required >
					<option value="">Election State</option>
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
					<label>Election's First Larger Party*</label>
					<select name="party_1" class="form-control inside-shadow" required >
						<option value="">Election's First Larger Party</option>
						<?php
							if($partyList=mysqli_query($connection, "SELECT party_id, party_name FROM ".TABLE_PREFIX."party ORDER BY party_name ASC")) {

								if(mysqli_num_rows($partyList)!=0) {
									while($partyListData=mysqli_fetch_assoc($partyList)) {
										echo "<option value='{$partyListData['party_id']}'>{$partyListData['party_name']}</option>";
									}
								} else {
									echo "<option value=''>No Party Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Election's Second Larger Party*</label>
					<select name="party_2" class="form-control inside-shadow" required >
						<option value="">Election's Second Larger Party</option>
						<?php
							if($partyList=mysqli_query($connection, "SELECT party_id, party_name FROM ".TABLE_PREFIX."party ORDER BY party_name ASC")) {

								if(mysqli_num_rows($partyList)!=0) {
									while($partyListData=mysqli_fetch_assoc($partyList)) {
										echo "<option value='{$partyListData['party_id']}'>{$partyListData['party_name']}</option>";
									}
								} else {
									echo "<option value=''>No Party Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Election's Third Larger Party</label>
					<select name="party_3" class="form-control inside-shadow" required >
						<option value="">Election's Third Larger Party</option>
						<?php
							if($partyList=mysqli_query($connection, "SELECT party_id, party_name FROM ".TABLE_PREFIX."party ORDER BY party_name ASC")) {

								if(mysqli_num_rows($partyList)!=0) {
									while($partyListData=mysqli_fetch_assoc($partyList)) {
										echo "<option value='{$partyListData['party_id']}'>{$partyListData['party_name']}</option>";
									}
								} else {
									echo "<option value=''>No Party Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Election's Fourth Larger Party</label>
					<select name="party_4" class="form-control inside-shadow" required >
						<option value="">Election's Fourth Larger Party</option>
						<?php
							if($partyList=mysqli_query($connection, "SELECT party_id, party_name FROM ".TABLE_PREFIX."party ORDER BY party_name ASC")) {

								if(mysqli_num_rows($partyList)!=0) {
									while($partyListData=mysqli_fetch_assoc($partyList)) {
										echo "<option value='{$partyListData['party_id']}'>{$partyListData['party_name']}</option>";
									}
								} else {
									echo "<option value=''>No Party Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Election Total Seats*</label>
					<input type="text" name="seat" class="form-control inside-shadow" placeholder="Election total seats..." value="<?php echo $election_total_seats; ?>" autofocus required >
				</div>
				<div class="form-group">
					<label>Election Type*</label>
					<select name="category" class="form-control inside-shadow" required >
						<option value="">Election type</option>
						<option value="lok_sabha">Lok Sabha</option>
						<option value="vidhan_sabha" selected >Vidhan Sabha</option>
					</select>
				</div>
				<div class="form-group">
					<label>Election Year*</label>
					<input type="text" name="year" class="form-control inside-shadow" placeholder="Election year..." value="<?php echo date('Y'); ?>" autofocus required >
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="<?php echo ((isset($_GET['id'])) ? 'update' : 'publish' ) ?>">
					<input type="hidden" name="action" value="<?php echo $postType; ?>">
					<input type="hidden" name="election_id" value="<?php echo $election_id; ?>">
					<input type="submit" id="submitForm" class="btn btn-green" value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add New' ); ?> election" >
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
						<th>Seats</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."election ORDER BY election_name ASC")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="election/?type=list&id=<?php echo $election_id; ?>" ><?php echo $election_name ?></a></p>
											<div class="hover">
												<a href="election.php?type=list&id=<?php echo $election_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<a href="delete.php?type=election&id=<?php echo $election_id; ?>"><i class="fas fa-trash"></i> Delete</a>
											</div>
										</td>
										<td><?php echo ucwords(str_replace('_', ' ', $election_state)); ?></td>
										<td><?php echo ucwords(str_replace('_', ' ', $election_type)); ?></td>
										<td><?php echo $election_total_seats; ?></td>
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