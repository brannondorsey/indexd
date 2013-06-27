<?php
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 //Session::start();
	 Database::init_connection();
	 $user = new User();
	 if(!empty($_POST) && isset($_POST)){ 
	 	$post_array = Database::clean($_POST);
	 	//below will actually use $post_array. Using get for testing
		 if(!$user->is_signed_in()){
		 	$user->sign_in($post_array['email'], $post_array['password']);
		 }
		 else echo "the user is signed in <br/>";
	}
	if($user->is_signed_in()){
		// foreach ($user->data as $property => $value) {
		// 	echo "the logged in user's " . $property . " is " . $value . "<br/>";
		// }
		
	}
	Database::close_connection();
?> 	