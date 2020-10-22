<?php

	$productStatus=mysqli_real_escape_string($connection, $_GET['status']);
	$startFrom=(isset($_GET['product']) ? $_GET['product'] : 0 );

	$productStatus=(($productStatus=='all') ? '' : "AND product_status='{$productStatus}'" );
	
?>
<h2>All products</h2>
<div class="options-bar">
	<div class="left-box">
		<form method="GET">
			<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" >
			<select class="form-control" name="status" id="changeStatus" >
				<option value="all" <?php echo (($_GET['status']=='all') ? 'selected' : '' ) ?> >All products</option>
				<option value="0" <?php echo (($_GET['status']=='0') ? 'selected' : '' ) ?> >Drafts products</option>
				<option value="1" <?php echo (($_GET['status']=='1') ? 'selected' : '' ) ?>  >Published products</option>
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
			<th style="width: 150px">Category</th>
			<th style="width: 150px">Author</th>
			<th style="width: 100px">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php

			echo "SELECT product_id, product_title, category_title, link_url, link_status, link_creation, profile_display_name FROM ".TABLE_PREFIX."product, ".TABLE_PREFIX."link, ".TABLE_PREFIX."category WHERE product_owner_id='".USER_ID."' AND link_type='product' AND link_relation_id=product_id AND link_site_id='".SITE_ID."' AND category_id=product_category AND link_status!=301 $productStatus ORDER BY link_published DESC LIMIT {$startFrom}, 10";

			if($dataList=mysqli_query($connection, "SELECT product_id, product_title, category_title, link_url, link_status, link_creation, profile_display_name FROM ".TABLE_PREFIX."product, ".TABLE_PREFIX."link, ".TABLE_PREFIX."category WHERE product_owner_id='".USER_ID."' AND link_type='product' AND link_relation_id=product_id AND link_site_id='".SITE_ID."' AND category_id=product_category AND link_status!=301 $productStatus ORDER BY link_published DESC LIMIT {$startFrom}, 10")) {
				if(mysqli_num_rows($dataList)!=0) {
					while($data=mysqli_fetch_assoc($dataList)) { extract($data) ?>
						<tr>
							<td>
								<p><a href="product.php?type=edit&editor=true&id=<?php echo $product_id; ?>" ><?php echo $product_title.(($link_status!='200') ? '<strong> — Draft </strong>' : '' ); ?></a></p>
								<div class="hover">
									<a href="product.php?type=edit&editor=true&id=<?php echo $product_id; ?>" ><i class="fas fa-pencil-alt"></i> Edit</a>
									<a href="product.php?type=product&id=<?php echo $product_id; ?>"><i class="fas fa-trash"></i> Delete</a>
									<a href="<?php echo $link_url; ?>" target='_blank' ><i class="fas fa-eye"></i> View</a>
								</div>
							</td>
							<td><?php echo $category_title; ?></td>
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