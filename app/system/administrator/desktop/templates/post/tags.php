<?php

	$tagID=mysqli_real_escape_string($connection, ((isset($_GET['tag_id'])) ? $_GET['tag_id'] : 0 ));
	if($tagData=mysqli_query($connection, "SELECT tag_id, tag_title FROM ".TABLE_PREFIX."tag WHERE tag_site_id='".SITE_ID."' AND tag_type='post' AND tag_id='{$tagID}'")) {
		if(mysqli_num_rows($tagData)!=0) {
			extract(mysqli_fetch_assoc($tagData));
		} else {
			$tag_title='';
			$tag_description='';
			$tag_parent_id='';
			$link_url='';
		}
	}

?>
<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> Categories <?php echo ((isset($_GET['tag_id'])) ? "<span><a href='post/?type=tags' class='btn btn-blue btn-sm' >Add New</a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" >
				<h3><?php echo ((isset($_GET['tag_id'])) ? 'Update' : 'Add New' ) ?> tag</h3>
				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" <?php echo ((!isset($_GET['tag_id'])) ? 'id="title"' : ''); ?> class="form-control inside-shadow" placeholder="tag name..." value="<?php echo $tag_title; ?>" autofocus >
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="category">
					<?php
						if(isset($_GET['category_id'])) {
							echo "<input type='hidden' name='oldURL' value='{$link_url}' >";
						}
					?>
					<input type="hidden" name="postType" value="<?php echo $postType ?>">
					<input type="hidden" name="categoryID" id="category_id" value="<?php echo $categoryID; ?>">
					<input type="submit" id="submitCategory" class="btn btn-green" value="<?php echo (isset($_GET['category_id']) ? 'Update' : 'Add' ); ?> Category" >
					<?php
					if(isset($_GET['category_id'])) {
						echo "<a href='' class='btn btn-blue'>Advance Edit</a>";
					}
					?>
				</div>
			</form>
		</div>
		<div class="category-list">
			<table id="table">
				<thead>
					<tr>
						<th style='width: 200px;'>Title</th>
						<th style='width: 50px'>Count</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT category_id, category_description, category_title, category_total_count, link_url, profile_display_name FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link, ".TABLE_PREFIX."profile WHERE category_id=link_relation_id AND link_type='category' AND category_type='{$postType}' AND category_site_id='".SITE_ID."' AND profile_owner_id='".USER_ID."' AND link_status=200 ORDER BY category_id DESC")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="post.php?type=edit&editor=true&post_id=<?php echo $category_id; ?>" ><?php echo $category_title ?></a></p>
											<div class="hover">
												<a href="post.php?type=taxonomy&editor=true&id=<?php echo $category_id; ?>&taxonomy=category" ><i class="fas fa-pencil-alt"></i> Advanced Edit</a>
												<a href="post.php?type=category&category_id=<?php echo $category_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<?php
													if($category_id!=1) { ?>
														<a href="delete.php?type=category&category_id=<?php echo $category_id; ?>"><i class="fas fa-trash"></i> Delete</a>
													<?php
													}
												?>
												<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
											</div>
										</td>
										<td><?php echo $category_total_count; ?></td>
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