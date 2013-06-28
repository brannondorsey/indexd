<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 require_once("classes/class.ContentOutput.inc.php");

	 Database::init_connection();
	 Session::start();
	 $api = new PrivateAPI();
	 $get_array = Database::clean($_GET);
	 echo $api->get_JSON_from_GET($get_array);
	 Database::close_connection();
?> 