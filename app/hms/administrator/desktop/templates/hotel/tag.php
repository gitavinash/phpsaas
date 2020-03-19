<?php

	$tagID=mysqli_real_escape_string($connection, ((isset($_GET['tag_id'])) ? $_GET['tag_id'] : 0 ));
	if($tagData=mysqli_query($connection, "SELECT tag_id, tag_title FROM ".TABLE_PREFIX."tag WHERE tag_site_id='".SITE_ID."' AND tag_type='{$postType}' AND tag_id='{$tagID}'")) {
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
<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> Categories <?php echo ((isset($_GET['tag_id'])) ? "<span><a href='{$postType}/?type=tag' class='btn btn-blue btn-sm' >Add New</a></span>" : '' ) ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" >
				<h3><?php echo ((isset($_GET['tag_id'])) ? 'Update' : 'Add New' ) ?> tag</h3>
				<div class="form-group">
					<label>Name</label>
					<input type="text" name="name" <?php echo ((!isset($_GET['tag_id'])) ? 'id="title"' : ''); ?> class="form-control inside-shadow" placeholder="Tag name..." value="<?php echo $tag_title; ?>" autofocus >
				</div>
				<div class="form-group">
					<input type="hidden" name="type" value="tag">
					<input type="hidden" name="postType" value="<?php echo $postType ?>">
					<input type="hidden" name="categoryID" id="tag_id" value="<?php echo $tagID; ?>">
					<input type="submit" id="submitCategory" class="btn btn-green" value="<?php echo (isset($_GET['tag_id']) ? 'Update' : 'Add' ); ?> Tag" >
					<?php
					if(isset($_GET['tag_id'])) {
						echo "<a href='' class='btn btn-blue'>Advance Edit</a>";
					}
					?>
				</div>
			</form>
		</div>
		<div class="category-list">
			<h3>Most Used Tags</h3>
			<div class="options-bar">
				<div class="left-box">
					<form method="GET">
						<input type="hidden" name="type" value="tag" >
						<select name="sort" class="form-control" >
							<option value="">Sort by</option>
							<option value="desc">Most Used Tag</option>
							<option value="asc">Minimum Used Tag</option>
						<input type="submit" class="btn" value="Filter" >
					</form>
				</div>
				<div class="right-box">
					
				</div>
			</div>
			<table id="table">
				<thead>
					<tr>
						<th style='width: 200px;'>Title</th>
						<th style='width: 50px'>Count</th>
					</tr>
				</thead>
				<tbody>
					<?php

						if($dataList=mysqli_query($connection, "SELECT tag_id, tag_title, count(tag_title) as totalTags FROM ".TABLE_PREFIX."tag use index (trending) WHERE tag_type = '{$postType}' AND tag_site_id='".SITE_ID."' group by tag_title ORDER BY totalTags DESC LIMIT 0, 10")) {
							if(mysqli_num_rows($dataList)!=0) {
								while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
									<tr>
										<td>
											<p><a href="<?php echo $postType ?>.php?type=edit&editor=true&post_id=<?php echo $tag_id; ?>" ><?php echo ucwords($tag_title) ?></a></p>
											<div class="hover">
												<a href="<?php echo $postType ?>.php?type=taxonomy&editor=true&id=<?php echo $tag_id; ?>&taxonomy=tag" ><i class="fas fa-pencil-alt"></i> Advanced Edit</a>
												<a href="<?php echo $postType ?>.php?type=tag&tag_id=<?php echo $tag_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
												<a href="delete.php?type=tag&tag_id=<?php echo $tag_id; ?>"><i class="fas fa-trash"></i> Delete</a>
												<a href="/tag/<?php echo strtolower(str_replace(' ', '-', $tag_title)); ?>/" target='_blank' ><i class="fas fa-eye"></i> View</a>
											</div>
										</td>
										<td><?php echo $totalTags; ?></td>
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