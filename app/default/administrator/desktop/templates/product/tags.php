<h2><?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?> categories <?php echo ((isset($_GET['id'])) ? "<span><a href='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "/?type=category' class='btn btn-blue btn-sm' >Add New</a></span>" : '') ?></h2>

<div class="row">
	<div class="category">
		<div class="tags-list">
			<table id="table">
				<thead>
					<tr>
						<th style='width: 200px;'>Title</th>
						<th style='width: 50px'>Count</th>
					</tr>
				</thead>
				<tbody>
					<?php

					if ($dataList = mysqli_query($connection, "SELECT COUNT(tag_id) as total_tag_count, tag_id, tag_title FROM `isw_tag` WHERE tag_site_id=" . SITE_ID . " GROUP BY tag_title ORDER BY tag_title ASC")) {
						if (mysqli_num_rows($dataList) != 0) {
							while ($data = mysqli_fetch_assoc($dataList)) {
								extract($data) ?>
								<tr style="height: 60px">
									<td>
										<p><a href="<?php echo $postType ?>/?type=taxonomy_tag&editor=true&post_id=<?php echo base64_encode($tag_id); ?>"><?php echo ucfirst($tag_title) ?></a></p>
										<div class="hover">
											<a href="<?php echo $postType ?>/?type=taxonomy_tag&editor=true&id=<?php echo base64_encode($tag_id); ?>&taxonomy=tag"><i class="fas fa-pencil-alt"></i> Edit</a>
											<?php
											if ($tag_id != 1) { ?>
												<a href="#" id="<?php echo base64_encode($tag_id) . '_tag' ?>" class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
											<?php
											}
											?>
											<a href="/tag/<?php echo str_replace(' ', '_', $tag_title) ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
										</div>
									</td>
									<td><?php echo $total_tag_count ?></td>
								</tr>
					<?php
							}
						}
					} else {
						echo mysqli_error($connection);
					}

					?>
				</tbody>
			</table>
		</div>
	</div>
</div>