<?php

	$deleteType=mysqli_real_escape_string($connection, $_GET['type']);
	$deleteID=mysqli_real_escape_string($connection, $_GET['id']);

	mysqli_query($connection, "START TRANSACTION");

	$deletePost=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."post WHERE post_id='{$deleteID}'");
	$deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$deleteID}'");
	$deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_relation_id='{$deleteID}'");

	if($deletePost && $deleteMeta && $deleteLink) {

		mysqli_query($connection, "COMMIT");
		$redirectUrl="/dashboard/{$deleteType}/?type=list&status=all"; ?>
		<script type="text/javascript">
			alert("<?php echo ucwords($deleteType).' Delete Successfully'; ?>")
			window.location.href="<?php echo $redirectUrl ?>";
		</script>
		<?php
		exit();

	} else {

		echo "system failure while deleting {$deleteType}";

	}