<?php

	$status=mysqli_real_escape_string($connection, $_GET['status']);
	$startFrom=mysqli_real_escape_string($connection, (isset($_GET['page']) ? $_GET['page']*10 : 0 ));

	$statusFilter=(($status=='all') ? 'AND link_status!=301' : "AND link_status='{$status}'" );

	$categoryID=mysqli_real_escape_string($connection,((isset($_GET['category'])) ? $_GET['category'] : 'all' ));
	$categoryFilter=(($categoryID=='all') ? '' : "AND {$postType}_category='{$categoryID}'" );

	$authorID=mysqli_real_escape_string($connection,((isset($_GET['author'])) ? $_GET['author'] : 'all' ));
	$authorFilter=(($authorID=='all') ? '' : "AND link_owner_id='{$authorID}'" );
	
?>
<h2>All <?php echo ucwords($postType.'s') ?></h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All <?php echo $postType ?></option>
				<option value="206" <?php echo (($_GET['status']=='206') ? 'selected' : '' ) ?> >Drafts <?php echo $postType ?></option>
				<option value="200" <?php echo (($_GET['status']=='200') ? 'selected' : '' ) ?>  >Published <?php echo $postType ?></option>
			</select>
			<select name="category" class="form-control">
				<option value="all">Select Category</option>
				<?php
					if($categoryList=mysqli_query($connection, "SELECT category_id, category_title FROM ".TABLE_PREFIX."category WHERE category_site_id='".SITE_ID."' AND category_type='{$postType}'")) {
						while($category=mysqli_fetch_assoc($categoryList)) { ?>
							<option value="<?php echo $category['category_id']; ?>" <?php echo (($category['category_id']==$categoryID) ? 'selected' : '' ) ?>  > <?php echo ucwords($category['category_title']); ?></option>
						<?php
						}
					}
				?>
			</select>
			<select name="author" class="form-control">
				<option value="all">Select Author</option>
				<?php
					if($authorList=mysqli_query($connection, "SELECT profile_owner_id, profile_display_name FROM ".TABLE_PREFIX."profile, ".TABLE_PREFIX."user WHERE user_id=profile_owner_id AND user_status=1 ORDER BY profile_display_name ASC")) {
						while($author=mysqli_fetch_assoc($authorList)) { ?>
							<option value="<?php echo $author['profile_owner_id']; ?>" <?php echo (($author['profile_owner_id']==$authorID) ? 'selected' : '' ) ?>  > <?php echo ucwords($author['profile_display_name']); ?></option>
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

			if($dataList=mysqli_query($connection, "SELECT {$postType}_id, {$postType}_title, link_url, link_status, link_creation, category_title,  profile_display_name FROM isw_{$postType} INNER JOIN isw_link ON link_relation_id={$postType}_id INNER JOIN isw_category ON category_id={$postType}_category INNER JOIN isw_profile ON profile_owner_id={$postType}_owner_id WHERE {$postType}_type='{$postType}' AND link_type='{$postType}' AND link_status!=301 {$statusFilter} {$authorFilter} {$categoryFilter} GROUP BY link_relation_id ORDER BY link_relation_id DESC LIMIT {$startFrom}, 10")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="news.php?type=edit&editor=true&id=<?php echo ${$postType.'_id'}; ?>" ><?php echo ${$postType.'_title'}.(($link_status!='200') ? '<strong> — Draft </strong>' : '' ); ?></a></p>
								<div class="hover">
									<?php
									
										if(${$postType.'_owner_id'}==USER_ID) { ?>

											<a href="news.php?type=edit&editor=true&id=<?php echo ${$postType.'_id'}; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
											<a href="delete.php?type=<?php echo $postType ?>&id=<?php echo ${$postType.'_id'}; ?>" onclick="return confirm('Are you sure you want to delete this <?php echo $postType ?>?');" ><i class="fas fa-trash"></i> Delete</a>

										<?php
										}

									?>
									<a href="news.php?type=edit&editor=true&id=<?php echo ${$postType.'_id'}; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $profile_display_name; ?></td>
							<td><?php echo $category_title ?></td>
							<td><?php echo date('d/m/Y', strtotime($link_creation)); ?></td>
						</tr>
					<?php
					}
				} else { ?>
					<tr>
						<td colspan="4">No News Found</td>
					</tr>
				<?php
				}
			}
			
		?>
	</tbody>
</table>

<div class="pagination-box">
	<ul class="pagination">
		<?php
		
		$currentPage=mysqli_real_escape_string($connection, (isset($_GET['page']) ? $_GET['page'] : 0 ));

		if($totalnews=mysqli_query($connection, "SELECT count({$postType}_id) as totalnews FROM ".TABLE_PREFIX."{$postType}, ".TABLE_PREFIX."link WHERE {$postType}_type='{$postType}' AND link_relation_id={$postType}_id AND link_type='{$postType}' {$categoryFilter} {$authorFilter} {$statusFilter}")) {
			
			if(mysqli_num_rows($totalnews)!=0) {
					
				extract(mysqli_fetch_assoc($totalnews));

				$totalnews=ceil($totalnews/10);

				if($startFrom==0) { ?>
					<li><a href='news/?type=list&status=<?php echo $status ?>&category=<?php echo $categoryID ?>&author=<?php echo $authorID; ?>&page=1'>Next</a></li>
				<?php
				} else { ?>
					<li><a href='news/?type=list&status=<?php echo $status ?>&category=<?php echo $categoryID ?>&author=<?php echo $authorID; ?>&page=<?php echo $currentPage-1; ?>'>Back</a></li>
					<li><a href='news/?type=list&status=<?php echo $status ?>&category=<?php echo $categoryID ?>&author=<?php echo $authorID; ?>&page=<?php echo $currentPage+1; ?>'>Next</a></li>
				<?php
				}

			} else {

				echo mysqli_error($connection);

			}

		}

		?>
	</ul>
</div>