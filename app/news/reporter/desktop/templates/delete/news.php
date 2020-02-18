<?php

	$deleteType=mysqli_real_escape_string($connection, $_GET['type']);
	$deleteID=mysqli_real_escape_string($connection, $_GET['id']);

	mysqli_query($connection, "START TRANSACTION");

	$deletenews=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."news WHERE news_id='{$deleteID}'");
	$deleteMeta=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."meta WHERE meta_relation_id='{$deleteID}'");
	$deleteLink=mysqli_query($connection, "DELETE FROM ".TABLE_PREFIX."link WHERE link_relation_id='{$deleteID}'");

	if($deletenews && $deleteMeta && $deleteLink) {

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