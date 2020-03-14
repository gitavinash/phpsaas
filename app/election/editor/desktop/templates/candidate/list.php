<?php

	$electionID=mysqli_real_escape_string($connection,((isset($_GET['election'])) ? $_GET['election'] : 'all' ));
	$electionFilter=(($electionID=='all') ? '' : "AND candidate_election='{$electionID}'" );

	$electionID=mysqli_real_escape_string($connection,((isset($_GET['election'])) ? $_GET['election'] : 'all' ));
	$electionFilter=(($electionID=='all') ? '' : "WHERE candidate_election='{$electionID}'" );
	
?>
<h2>All <?php echo ucwords($postType.'s') ?></h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select name="election" class="form-control">
				<option value="all">Select Election</option>
				<?php
					if($electionList=mysqli_query($connection, "SELECT election_id, election_name FROM ".TABLE_PREFIX."election ORDER BY election_name ASC")) {
						while($election=mysqli_fetch_assoc($electionList)) { ?>
							<option value="<?php echo $election['election_id']; ?>" <?php echo (($election['election_id']==$electionID) ? 'selected' : '' ) ?>  > <?php echo ucwords($election['election_name']); ?></option>
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
</div>
<table id="table">
	<thead>
		<tr>
			<!-- <th style="width: 50px"></th> -->
			<th>Title</th>
			<th style="width: 150px">Election</th>
			<th style="width: 100px">Constituency</th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."candidate {$electionFilter} ORDER BY candidate_name ASC")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="candidate.php?type=edit&editor=true&id=<?php echo $candidate_id ?>" ><?php echo $candidate_name ?></a></p>
								<div class="hover">
									<a href="candidate.php?type=edit&editor=true&id=<?php echo $candidate_id ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="delete.php?type=candidate&id=<?php echo $candidate_id ?>" onclick="return confirm('Are you sure you want to delete this candidate?');" ><i class="fas fa-trash"></i> Delete</a>
								</div>
							</td>
							<td><?php echo $profile_display_name; ?></td>
							<td><?php echo $category_title ?></td>
						</tr>
					<?php
					}
				} else { ?>
					<tr>
						<td colspan="4">No candidate Found</td>
					</tr>
				<?php
				}
			} else {
				echo mysqli_error($connection);
			}
			
		?>
	</tbody>
</table>