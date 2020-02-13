<?php

	if($_SERVER['REQUEST_METHOD']=='POST') {

		foreach($_POST as $key => $value) {
			$secure[$key]=mysqli_real_escape_string($connection, $value);
		}
		
		switch ($secure['type']) {

			case 'api':
				header("Content-type: application/json; charset=utf-8");
				$includeFile = ROOT.'/api/'.$secure['action'].'.php';
				break;
			
			case 'authentication' :
				header("Content-type: application/json; charset=utf-8");
				$includeFile = MODULES.$secure['type'].'.php';
				break;

			case 'draft' :
				if(isset($secure['post_id'])) {
					$secure['action']='update';
				} else {
					$secure['action']='add';
				}
				$includeFile = APP_MODULES.$secure['type'].'.php';
				break;

			default:

				if(file_exists(MODULES.$secure['type'].'.php')) {

					$includeFile=MODULES.$secure['type'].'.php';

				} else {

					$includeFile = APP_MODULES.$secure['type'].'.php';

				}

				break;
		}

		if(file_exists($includeFile)) {

			require_once $includeFile;

			if(function_exists($secure['action'])) {

				call_user_func($secure['action'], $connection, $secure);

			} else {

				echo '<h1>Undefine function '.$secure['type'].' -> '.$secure['action'].' </h1>';

			}

		} else {

			echo 'Unable to find current request method -> '.$includeFile;

		}

	}

	exit();