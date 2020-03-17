<?php

	$status=mysqli_real_escape_string($connection, (isset($_GET['status'])) ? $_GET['status'] : 0 );

?>

<h2>All <?php echo ucwords($postType.'s') ?></h2>

<div class="options-bar">
	
</div>

<table id="table">
	<thead>
		<tr>
			<!-- <th style="width: 50px"></th> -->
			<th>Profile Display Name</th>
			<th style="width: 100px">Email</th>
			<th style="width: 100px">User Type</th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT profile_display_name, user_id, user_email, user_type FROM ".TABLE_PREFIX."profile, ".TABLE_PREFIX."user WHERE profile_owner_id=user_id AND user_parent_id='".USER_ID."'")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="post.php?type=edit&editor=true&post_id=<?php echo $user_id; ?>" ><?php echo $profile_display_name ?></a></p>
								<div class="hover">
									<a href="user.php?type=edit&editor=true&id=<?php echo $user_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="delete.php?type=user&id=<?php echo $user_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									<a href="" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $user_email; ?></td>
							<td><?php echo $user_type; ?></td>
						</tr>
					<?php
					}
				}
			}
			
		?>
	</tbody>
</table>