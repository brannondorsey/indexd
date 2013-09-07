<?php 
	require_once 'classes/class.Database.inc.php';
	require_once 'classes/class.User.inc.php';

	require_once 'database_info.inc.php';
	Database::init_connection($host, $database, $table, $username, $password);

	$user = new User();
	$email = "brannon@brannondorsey.com";
	echo ($user->send_reset_password_email($email) ? "true" : "false");
	Database::close_connection();
?>