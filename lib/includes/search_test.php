<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 require_once("classes/class.ContentOutput.inc.php");

	 require_once 'database_info.inc.php';
	 $api = new PrivateAPI($host, $database, $table, $username, $password);
	 Session::start();
	 
	 $get_array = Database::clean($_GET);
	 echo $api->get_json_from_assoc($get_array);
	 Database::close_connection();
?> 