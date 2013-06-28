<?php 
	require_once 'classes/class.Database.inc.php';
	require_once 'classes/class.User.inc.php';
	Database::init_connection();
	$user = new User();
	$email = "real@real.com";
	echo ($user->email_already_exists($email) ? "true" : "false");
	Database::close_connection();
?>