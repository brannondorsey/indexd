<?php
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 Database::init_connection();
	 $user = new User();
	 Session::start();

	 if(!empty($_POST) && isset($_POST)){ 
	 	$post_array = Database::clean($_POST);
		if(!$user->is_signed_in()){
		 	if($success = $user->sign_in($post_array['email'], $post_array['password'])){
		 		if($success == -1) echo "email not confirmed";
		 		else echo "just signed in";
		 	}
		 	else echo "user authentication failed"; //code for failed login
		}
		else echo "the user is signed in <br/>";
	}
	// if($user->is_signed_in()){
	// 	foreach ($user->data as $property => $value) {
	// 		echo "the logged in user's " . $property . " is " . $value . "<br/>";
	// 	}
	// }
	Database::close_connection();
?> 	