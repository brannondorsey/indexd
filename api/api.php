<?php
	 require_once("../lib/includes/classes/class.PrivateAPI.inc.php");
	 require_once("../lib/includes/classes/class.API.inc.php");
	 //header("Content-Type: text/javascript; charset=utf-8");
	 Database::init_connection();
	 $api = new PrivateAPI();
	 $get_array = Database::clean($_GET); //clean the $_GET array
	 $data = $api->get_JSON_from_GET($get_array); //return user JSON objs based on API get params 
	 Database::close_connection();
	 echo $data;
?>