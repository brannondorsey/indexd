<?php
	require_once 'lib/includes/classes/class.User.inc.php';
	require_once 'lib/includes/classes/class.Database.inc.php';
	Database::init_connection();
	$user = new User();
	$get_array = Database::clean($_GET);
	if(isset($get_array['email']) &&
	   isset($get_array['email_confirmation_code'])){
		//if confirmation is correct and `email_verified` is changed to 1 in the db 
		if($user->confirm_email($get_array['email'], $get_array['email_confirmation_code'])){
			header( 'Location: ' . Database::$root_dir_link);
			echo "I tried to redirect the page";
		}
	}
	else echo "Error: email and confirmation code are invalid or were not supplied";
	Database::close_connection();
?>