<?php

	session_destroy();

	if (isset($_SERVER['HTTP_COOKIE'])) {
	    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	    foreach($cookies as $cookie) {
	        $parts = explode('=', $cookie);
	        $name = trim($parts[0]);
	        setcookie($name, '', time()-1000);
	        setcookie($name, '', time()-1000, '/');
	    }
	}

	if(file_exists(USER_VIEW_PATH.'logout.php')) {

		include_once USER_VIEW_PATH.'logout.php';

	} else {

		header("Location: /login/?logout=true");

	}

	exit();