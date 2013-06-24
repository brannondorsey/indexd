<?php
	 require_once("../lib/includes/classes/class.PrivateAPI.inc.php");
	 require_once("../lib/includes/classes/class.API.inc.php");
	 //header("Content-Type: text/javascript; charset=utf-8");
	 $api = new PrivateAPI();
	 $get_array = $api->db->clean($_GET); //clean the $_GET array
	 $data = $api->get_JSON_from_GET($get_array); //return user JSON objs based on API get params 
	 echo $data;
?>