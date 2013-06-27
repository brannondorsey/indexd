<?php
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 Database::init_connection();
	 Session::start();
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
		echo $user->data->email;
	}
	Database::close_connection();
?> 	