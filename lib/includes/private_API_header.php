<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 require_once("classes/class.User.inc.php");
	 Database::init_connection();
	 $algorithm = new RelationalAlgorithm();
	 $api = new PrivateAPI();
	 $get_array = Database::clean($_GET);
	 $user = new User($get_array['id']);
	 echo $user->data->first_name;
	 //$algorithm->get_related_users((int) $get_array['id']);
	 Database::close_connection();
	 //echo $data;
?> 