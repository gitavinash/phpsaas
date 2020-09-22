<h2>All <?php echo ucwords($postType.'s') ?></h2>
<!--
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $postType ?></option>
				<option value="0" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Drafts <?php echo $postType ?></option>
				<option value="1" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Published <?php echo $postType ?></option>
			</select>
			<select name="category" class="form-control">
				<option value="all">Select Category</option>
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_name FROM ".TABLE_PREFIX."category WHERE category_site_id='".SITE_ID."'")) {
						while($category=mysqli_fetch_assoc($categoryList)) { ?>
							<option value="<?php echo $category['category_id']; ?>" <?php echo (($category['category_id']==$categoryID) ? 'selected' : '' ) ?>  > <?php echo ucwords($category['category_name']); ?></option>
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
-->
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

			if($dataList=mysqli_query($connection, "SELECT profile_display_name, user_id, user_email, user_type FROM itssoftworld_profile, itssoftworld_user WHERE profile_user_id=user_id AND user_parent_id=".USER_ID)) {
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