<?php

	if(isset($_GET['tag'])) {

		$status=404;

		$tag=mysqli_real_escape_string($connection, $_GET['tag']);

		if($tags=mysqli_query($connection, "SELECT name FROM tag WHERE name LIKE '%{$tag}%' LIMIT 0, 10")) {
			if(mysqli_num_rows($tags)!=0) {
				$status=200;
				while ($tagList=mysqli_fetch_assoc($tags)) {
					$tagName[]=$tagList['name'];
				}
			}
		}

		$result = array(
			'status' => $status,
			'msg' => ((isset($tagName)) ? $tagName : '')
		);

	}

	echo json_encode($result, true);

?>