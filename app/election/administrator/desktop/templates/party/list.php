<?php

	$partyId=mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0 ));
	if($categoryData=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."party WHERE party_id='{$partyId}'")) {
		if(mysqli_num_rows($categoryData)!=0) {
			extract(mysqli_fetch_assoc($categoryData));
		} else {
            $party_id='';
            $party_code='';
            $party_name='';
            $party_slogan='';
            $party_logo='';
		}
	}

?>
<h2>Manage <?php echo ucwords($postType); ?> <?php echo ((isset($_GET['id'])) ? "<span><a href='party/?type=list' class='btn btn-blue btn-sm' >Add New </a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="formData" >
				<h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New ' ).ucwords($postType); ?> </h3>
				<div class="form-group">
					<label>Party Name*</label>
					<input type="text" name="name" class="form-control inside-shadow" placeholder="Party Name..." value="<?php echo $party_name; ?>" autofocus required >
				</div>
				<div class="form-group">
					<label>Party Code*</label>
					<input type="text" name="code" class="form-control inside-shadow" placeholder="Party Code..." value="<?php echo $party_code; ?>"required >
				</div>
				<div class="form-group">
					<label>Party Slogan</label>
					<input type="text" name="slogan" class="form-control inside-shadow" placeholder="Party Slogan..." value="<?php echo $party_slogan; ?>" >
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="<?php echo ((isset($_GET['id'])) ? 'update' : 'publish' ) ?>">
					<input type="hidden" name="action" value="<?php echo $postType; ?>">
					<input type="hidden" name="party_id" value="<?php echo $party_id; ?>">
					<input type="submit" id="submitForm" class="btn btn-green" value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add New' ); ?> Party" >
				</div>
			</form>
		</div>
		<div class="category-list">
			<h3><?php echo ucwords($postType); ?> List</h3>
			<table id="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Code</th>
						<th>Symbol</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."party ORDER BY party_name ASC")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="party/?type=list&id=<?php echo $party_id; ?>" ><?php echo $party_name ?></a></p>
											<div class="hover">
												<a href="party.php?type=list&id=<?php echo $party_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<a href="delete.php?type=party&id=<?php echo $party_id; ?>"><i class="fas fa-trash"></i> Delete</a>
											</div>
										</td>
										<td><?php echo strtoupper(str_replace('_', ' ', $party_code)); ?></td>
										<td><?php echo strtoupper(str_replace('_', ' ', $party_logo)); ?></td>
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