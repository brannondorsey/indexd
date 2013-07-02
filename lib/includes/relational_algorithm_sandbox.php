<?php require_once 'classes/class.RelationalAlgorithm.inc.php';
	  require_once 'classes/class.User.inc.php';

	$algorithm = new RelationalAlgorithm();
	$user = new User();
	Database::init_connection();
	$get_array = Database::clean($_GET);
	if(isset($get_array['id'])){
		$user_id = $get_array['id'];
		print_users_algorithm_info($user_id);
		echo "<br/><br/>";
	 	echo $algorithm->get_related_users($user_id);
	}
	Database::close_connection();

	function print_users_algorithm_info($user_id){
		global $user;
		$obj = $user->get_user_data_obj($user_id);
		echo "ID: " . $obj->id . "<br/>";
		echo "First Name: " . $obj->first_name . "<br/>";
		echo "Last Name: " . $obj->last_name . "<br/>";
		echo "Media: " . $obj->media . "<br/>";
		echo "Tags: " . $obj->tags . "<br/>";
		echo "Location: " . $obj->city . ", " . $obj->state . ", " . strtoupper($obj->country) . "<br/>";
	}

	
?>