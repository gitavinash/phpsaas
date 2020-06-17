<?php

	if($_SERVER['REQUEST_METHOD']=='POST') {

		foreach($_POST as $key => $value) {
			$secure[$key]=mysqli_real_escape_string($connection, $value);
		}

		extract($secure);

		switch ($type) {
			
			case 'api' :
				header("Content-type: application/json; charset=utf-8");
				$includeFile = API.$action.'/'.$siteAction.'.php';
				break;

			case 'auth' :
				header("Content-type: application/json; charset=utf-8");
				$includeFile = API.$type.'/'.$action.'.php';
				break;

			case 'draft' :
				if(isset($secure['post_id'])) {
					$secure['action']='update';
				} else {
					$secure['action']='add';
				}
				$includeFile = APP_MODULES.$type.'.php';
				break;

			default:

				if(file_exists(MODULES.$type.'.php')) {

					$includeFile=MODULES.$type.'.php';

				} else {

					$includeFile = APP_MODULES.$type.'.php';

				}

				break;
		}

		if(file_exists($includeFile)) {

			require_once $includeFile;

			if($type!='api') {

				if(function_exists($secure['action'])) {

					call_user_func($secure['action'], $connection, $secure);
	
				} else {
					echo $includeFile;
					echo '<h1>Undefine function '.$type.' -> '.$secure['action'].' </h1>';
	
				}

			}

		} else {

			echo 'Unable to find current request method -> '.$includeFile;

		}

	}

	exit();