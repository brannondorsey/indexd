<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 Database::init_connection();
	 Session::start();
	 $api = new PrivateAPI();
	 $user = new User();
	 if(!empty($_GET) && isset($_GET)){ 
	 	$get_array = Database::clean($_GET);
	 	//below will actually use $post_array. Using get for testing
		 if(!$user->is_signed_in()){
		 	$user->sign_in($get_array['email'], $get_array['password']);
		 }
		 else echo "the user is signed in <br/>";
	}
	//if($user->is_signed_in()) echo $user->data->first_name;
	//pseudo logout using get for temporary testing
	if(isset($get_array['logout']) && strtolower($get_array['logout']) == "true"){
		$user->sign_out();
	}
	if($user->is_signed_in()){
		
		// $object_vars = get_object_vars($user->data);
		// foreach($object_vars as $property_name => $property_value){
		// 	echo "the user's " . $property_name . " is " . $property_value . "<br/>";
		// }
	}
	Database::close_connection();
?> 	