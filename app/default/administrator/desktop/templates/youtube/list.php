<?php

	$postStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$startFrom=(isset($_GET['page']) ? $_GET['page'] : 0 );

	$postStatus=(($postStatus=='all') ? '' : "AND video_status='{$postStatus}'" );
	
?>
<h2>All <?php echo ucwords($postType.'s') ?></h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All video</option>
				<option value="live" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Drafts video</option>
				<option value="video" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Published video</option>
			</select>
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All video type</option>
				<option value="live" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >live video</option>
				<option value="video" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >general video</option>
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
			<th style="width: 100px">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php

			if($dataList=mysqli_query($connection, "SELECT video_id, video_title ,link_url, link_status, link_creation, profile_display_name FROM ".TABLE_PREFIX."video, ".TABLE_PREFIX."link, ".TABLE_PREFIX."profile WHERE video_owner_id='".USER_ID."' AND link_type='video' AND link_relation_id=video_id AND link_site_id='".APP_SITE_ID."' AND profile_user_id='".USER_ID."' $postStatus ORDER BY link_published DESC LIMIT {$startFrom}, 10")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="youtube.php?type=edit&editor=true&page=<?php echo $video_id; ?>" ><?php echo $video_title.(($link_status!='200') ? '<strong> — Draft </strong>' : '' ); ?></a></p>
								<div class="hover">
									<a href="youtube.php?type=edit&editor=true&id=<?php echo $video_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="youtube.php?type=delete&id=<?php echo $video_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $profile_display_name; ?></td>
							<td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
						</tr>
					<?php
					}
				}
			}
			
		?>
	</tbody>
</table>