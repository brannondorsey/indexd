<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");

	 Database::init_connection();
	 Session::start();
	 $api = new PrivateAPI();
	 $user = new User();
	 if($_POST){
		 $post_array = Database::clean($_POST);
		 $post_array['country'] = "us"; //add country manually for now
		 if($user->register($post_array)) echo "registered successfully and email sent";
		 else echo "email not sent";
	}
	 Database::close_connection();
?> 