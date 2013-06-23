<?php
	require_once("../lib/includes/classes/class.PrivateAPI.inc.php");
	//require_once("../lib/includes/classes/class.API.inc.php");
	 $api = new PrivateAPI();
	 $get_array = $api->db->clean($_GET); //clean the $_GET array
	 $api->echo_JSON_from_GET($get_array); //return user JSON objs based on API get params 
?>