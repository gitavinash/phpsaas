<h2>Add new <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>
<div class='uploadBox'>
	<form id='uploadFile' class='uploader-box'>
		<h3>Drag &amp; drop files here</h3>
		<div class='form-group'>
			<span id='uploadText' >Open the file Browser</span>
			<input type='file' name='upload' />
			<input type='hidden' name='type' value='api' />
			<input type='hidden' name='action' value='singleUpload' />
		</div>
	</form>
	<div class="notice"><strong>only these file formats are supported:</strong> .png, .jpg, .jpeg, .gif, .doc, .docx, .xls, .xlsx, .pdf, .txt <strong>Maximum upload limit is 300 MB</strong></div>
</div>