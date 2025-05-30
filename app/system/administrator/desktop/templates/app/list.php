<?php

$id = base64_decode(mysqli_real_escape_string($connection, ((isset($_GET['id'])) ? $_GET['id'] : 0)));

if ($id != 0) {

	if ($appData = mysqli_query($connection, "SELECT app_title, app_description, app_path, app_visiblity FROM " . TABLE_PREFIX . "app WHERE app_id='{$id}'")) {

		if (mysqli_num_rows($appData) != 0) {

			extract(mysqli_fetch_assoc($appData));
		}
	}
} else {

	$app_title = '';
	$app_description = '';
	$app_path = '';
	$app_visiblity = '';
}

?>
<h2>Manage <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>s <?php echo ((isset($_GET['id'])) ? "<span><a href='" . rtrim(ltrim(ADMIN_BASE_URL, '/'), '/') . "/?type=list' class='btn btn-blue btn-sm' >Add New</a></span>" : '') ?></h2>

<div class="row">
	<div class="category">
		<div class="form-box">
			<form method="POST" id="categoryData" onSubmit="submitCategory()">
				<h3><?php echo ((isset($_GET['id'])) ? 'Update' : 'Add new') ?> category</h3>
				<div class="form-group">
					<label>App name*</label>
					<input type="text" name="title" class='form-control inside-shadow' value="<?php echo $app_title ?>" autofocus required>
				</div>
				<div class="form-group">
					<label>About app*</label>
					<textarea name="description" id="description" class='form-control inside-shadow' cols="30" rows="10"><?php echo $app_description ?></textarea>
				</div>
				<div class="form-group">
					<label>Directory ( path name of the app folder )*</label>
					<input type="text" name="path" class='form-control inside-shadow' value="<?php echo $app_path ?>" <?php echo ((($id != 0)) ? 'readonly' : '') ?>>
				</div>
				<div class="form-group">
					<input type="checkbox" name="visiblity" value=1 <?php echo (($app_visiblity == 1) ? 'checked' : '') ?>> Visible to all users
				</div>
				<div class="form-group">
					<input type="hidden" name="type" id="type" value="app">
					<input type="hidden" name="action" value="publish">
					<input type="hidden" name="appID" id="category_id" value="<?php echo $id; ?>">
					<input type="submit" id="submitCategory" class="btn btn-green" value="<?php echo (isset($_GET['id']) ? 'Update' : 'Add new'); ?> app">
					<?php
					if (isset($_GET['id'])) {
						echo "<a href='{$postType}/?type=taxonomy&editor=true&id={$id}&taxonomy=category' class='btn btn-blue'>Advance Edit</a>";
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
						<th style='width: 50px'>Visiblity</th>
					</tr>
				</thead>
				<tbody>
					<?php

					if ($dataList = mysqli_query($connection, "SELECT app_id, app_title, app_description, app_path, app_visiblity FROM " . TABLE_PREFIX . "app ORDER BY app_title ASC")) {
						if (mysqli_num_rows($dataList) != 0) {
							while ($data = mysqli_fetch_assoc($dataList)) {
								extract($data) ?>
								<tr>
									<td height="40px">
										<p><a href="<?php echo $postType ?>/?type=edit&editor=true&post_id=<?php echo base64_encode($app_id); ?>"><?php echo $app_title ?></a></p>
										<div class="hover">
											<a href="<?php echo $postType ?>/?type=list&id=<?php echo base64_encode($app_id); ?>"><i class="fas fa-pencil-alt"></i> Edit</a>
											<a href="#" id="<?php echo base64_encode($app_id) ?>_app" class="deleteBtn"><i class="fas fa-trash"></i> Delete</a>
											<a href="<?php echo $link_url; ?>" target='_blank'><i class="fas fa-eye"></i> View</a>
										</div>
									</td>
									<td><?php echo (($app_visiblity == 1) ? 'Public' : 'Private') ?></td>
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