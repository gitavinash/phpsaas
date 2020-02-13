<?php

	$postStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$startFrom=(isset($_GET['page']) ? $_GET['page'] : 0 );

	$postStatus=(($postStatus=='all') ? '' : "AND post_status='{$postStatus}'" );

	$categoryID=mysqli_real_escape_string($connection,((isset($_GET['category'])) ? $_GET['category'] : 'all' ));
	$postCategory=(($categoryID=='all') ? '' : "AND post_category='{$categoryID}'" );
	
?>
<h2>All <?php echo ucwords($postType.'s') ?></h2>
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
<table id="table">
	<thead>
		<tr>
			<!-- <th style="width: 50px"></th> -->
			<th>Title</th>
			<th style="width: 150px">Author</th>
			<th style="width: 100px">Categories</th>
			<th style="width: 100px">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php
				if($dataList=mysqli_query($connection, "SELECT post_id, post_title, category_name ,link_url, link_status, link_creation, profile_display_name FROM ".TABLE_PREFIX."post, ".TABLE_PREFIX."category, ".TABLE_PREFIX."link, ".TABLE_PREFIX."profile WHERE post_owner_id='".USER_ID."' AND category_id=post_category AND link_type='{$postType}' AND link_relation_id=post_id AND link_site_id='".SITE_ID."' AND profile_owner_id='".USER_ID."' $postStatus $postCategory ORDER BY link_published DESC LIMIT {$startFrom}, 10")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="post.php?type=edit&editor=true&post_id=<?php echo $post_id; ?>" ><?php echo $post_title.(($link_status!='200') ? '<strong> — Draft </strong>' : '' ); ?></a></p>
								<div class="hover">
									<a href="post.php?type=edit&editor=true&post_id=<?php echo $post_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="delete.php?type=post&post_id=<?php echo $post_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $profile_display_name; ?></td>
							<td><?php echo $category_name ?></td>
							<td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
						</tr>
					<?php
					}
				}
			}
			
		?>
	</tbody>
</table>