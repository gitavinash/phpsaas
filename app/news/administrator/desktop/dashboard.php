<h2 style="margin-bottom: 20px" >Daily Work Report</h2>
<?php

	$fromDate=date('Y-m-d 00:00:00');
	$toDate=date('Y-m-d 23:59:00');

	if($userList=mysqli_query($connection, "SELECT user_id, profile_display_name FROM ".TABLE_PREFIX."user INNER JOIN ".TABLE_PREFIX."profile ON user_id=profile_owner_id INNER JOIN ".TABLE_PREFIX."site_access ON site_access_user_id=user_id WHERE site_access_site_id=".SITE_ID." AND site_access_status=1 AND user_type!='administrator'")) {

		if(mysqli_num_rows($userList)!=0) {

			while($userListData=mysqli_fetch_assoc($userList)) {

				extract($userListData);

				if($countLivePost=mysqli_query($connection, "SELECT count(DISTINCT news_id) as publishedNews FROM ".TABLE_PREFIX."news INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id WHERE news_status=1 AND link_status=200 AND link_type='news' AND link_owner_id={$user_id} AND link_published BETWEEN '{$fromDate}' AND '{$toDate}'")) {

					extract(mysqli_fetch_assoc($countLivePost));

				}

				// if($countDraftPost=mysqli_query($connection, "SELECT count(DISTINCT news_id) as draftNews FROM ".TABLE_PREFIX."news INNER JOIN ".TABLE_PREFIX."link ON link_relation_id=news_id WHERE news_status=1 AND link_status!=206 AND link_type='news' AND link_owner_id={$user_id} AND link_published BETWEEN '{$fromDate}' AND '{$toDate}'")) {

				// 	extract(mysqli_fetch_assoc($countDraftPost));

				// }


				echo "<div class='work-report'><div class='username'>{$profile_display_name}</div><div class='publish'><span>Published</span>{$publishedNews}</div></div>";

			}

		} else {

			echo 'No User Found';

		}


	} else {

		echo mysqli_error($connection);

	}

?>