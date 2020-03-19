<?php

	$categoryID=mysqli_real_escape_string($connection, ((isset($_GET['category_id'])) ? $_GET['category_id'] : 0 ));
	if($categoryData=mysqli_query($connection, "SELECT category_parent_id, category_title, category_description, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_id=link_relation_id AND category_id='{$categoryID}' AND link_type='category' AND category_type='location' AND link_status=200")) {
		if(mysqli_num_rows($categoryData)!=0) {
			extract(mysqli_fetch_assoc($categoryData));
		} else {
			$category_title='';
			$category_description='';
			$category_parent_id='';
			$link_url='';
		}
	}

?>
<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> Locations <?php echo ((isset($_GET['location_id'])) ? "<span><a href='{$postType}/?type=location' class='btn btn-blue btn-sm' >Add New</a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" >
				<h3><?php echo ((isset($_GET['location_id'])) ? 'Update' : 'Add New' ) ?> Location</h3>
				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" <?php echo ((!isset($_GET['location_id'])) ? 'id="title"' : ''); ?> class="form-control inside-shadow" placeholder="Location name..." value="<?php echo $category_title; ?>" autofocus >
				</div>
				<div class="form-group">
					<label>Permalink</label>
					<input type="text" name="url" id='url' class="form-control inside-shadow" placeholder="Location url..." value="<?php $categoryURL=explode('/', ltrim(rtrim($link_url, '/'), '/')); echo end($categoryURL); ?>" >
				</div>
				<div class="form-group">
					<label>Parent Location</label>
					<select name="category" class="form-control inside-shadow">
						<option value="0">Select Parent Location</option>
						<?php
							if($categoryList=mysqli_query($connection, "SELECT category_id, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_site_id='".SITE_ID."' AND link_type='category' AND link_relation_id=category_id AND category_id!='{$categoryID}' AND link_status=200 AND category_type='post'")) {
								while($category=mysqli_fetch_assoc($categoryList)) { ?>
										<option value="<?php echo $category['category_id'].'_'.str_replace(SITE_DOMAIN.'/category', '', $category['link_url']); ?>" <?php echo (($category['category_id']==$category_parent_id) ? 'selected' : '' ) ?> > <?php echo ucwords($category['category_title']); ?></option>
								<?php
								}
							}
						?>
					</select>
				</div>
				<div class="form-group">
					<label>Description</label>
					<textarea name="description" class="form-control inside-shadow" placeholder="About location..."><?php echo $category_description; ?></textarea>
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="category">
					<?php
						if(isset($_GET['category_id'])) {
							echo "<input type='hidden' name='oldURL' value='{$link_url}' >";
						}
					?>
					<input type="hidden" name="postType" value="location">
					<input type="hidden" name="categoryID" id="category_id" value="<?php echo $categoryID; ?>">
					<input type="hidden" name="website_id" value="<?php echo SITE_ID ?>" >
					<input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>" >
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
						<th style='width: 200px;'>Location</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT category_id, category_description, category_title, link_url FROM ".TABLE_PREFIX."category, ".TABLE_PREFIX."link WHERE category_id=link_relation_id AND link_type='category' AND category_type='location' AND category_site_id='".SITE_ID."' AND link_status=200 ORDER BY category_id DESC")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="<?php echo $postType ?>.php?type=edit&editor=true&post_id=<?php echo $category_id; ?>" ><?php echo $category_title ?></a></p>
											<div class="hover">
												<a href="<?php echo $postType ?>.php?type=taxonomy&editor=true&id=<?php echo $category_id; ?>&taxonomy=category" ><i class="fas fa-pencil-alt"></i> Advanced Edit</a>
												<a href="<?php echo $postType ?>.php?type=location&category_id=<?php echo $category_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<?php
													if($category_id!=1) { ?>
														<a href="delete.php?type=location&category_id=<?php echo $category_id; ?>"><i class="fas fa-trash"></i> Delete</a>
													<?php
													}
												?>
												<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
											</div>
										</td>
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