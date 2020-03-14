<?php

	$candidateId=mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0 ));
	if($categoryData=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."candidate WHERE candidate_id='{$candidateId}'")) {
		if(mysqli_num_rows($categoryData)!=0) {
			extract(mysqli_fetch_assoc($categoryData));
		} else {
            $candidate_id='';
            $candidate_name='';
            $candidate_party='';
            $candidate_election='';
            $candidate_constituency='';
            $candidate_result='';
            $candidate_year='';
		}
	}

?>
<h2>Manage <?php echo ucwords($postType); ?> <?php echo ((isset($_GET['id'])) ? "<span><a href='candidate/?type=list' class='btn btn-blue btn-sm' >Add New </a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="formData" >
				<h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add New ' ).ucwords($postType); ?> </h3>
				<div class="form-group">
					<label>Candidate Name</label>
					<input type="text" name="name" class="form-control inside-shadow" placeholder="Candidate name..." value="<?php echo $candidate_name; ?>" autofocus required >
				</div>
				<div class="form-group">
					<label>Candidate Election</label>
					<select name="election" class="form-control inside-shadow" required >
						<option value="">Candidate Election</option>
						<?php
							if($electionList=mysqli_query($connection, "SELECT election_id, election_name FROM ".TABLE_PREFIX."election ORDER BY election_name ASC")) {

								if(mysqli_num_rows($electionList)!=0) {
									while($electionListData=mysqli_fetch_assoc($electionList)) {
										echo "<option value='{$electionListData['election_id']}'>{$electionListData['election_name']}</option>";
									}
								} else {
									echo "<option value=''>No Election Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Candidate Party</label>
					<select name="party" class="form-control inside-shadow" required >
						<option value="">Candidate Party Name</option>
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
					<label>Candidate Constituency</label>
					<select name="constituency" class="form-control inside-shadow" required >
						<option value="">Candidate Constituency Name</option>
						<?php
							if($constituencyList=mysqli_query($connection, "SELECT constituency_id, constituency_name FROM ".TABLE_PREFIX."constituency ORDER BY constituency_name ASC")) {

								if(mysqli_num_rows($constituencyList)!=0) {
									while($constituencyListData=mysqli_fetch_assoc($constituencyList)) {
										echo "<option value='{$constituencyListData['constituency_id']}'>{$constituencyListData['constituency_name']}</option>";
									}
								} else {
									echo "<option value=''>No Constituency Found</option>";
								}

							}
						?>
					</select>
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="<?php echo ((isset($_GET['id'])) ? 'update' : 'publish' ) ?>">
					<input type="hidden" name="action" value="<?php echo $postType; ?>">
					<input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>">
					<input type="submit" id="submitForm" class="btn btn-green" value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add New' ); ?> Candidate" >
				</div>
			</form>
		</div>
	</div>
</div>