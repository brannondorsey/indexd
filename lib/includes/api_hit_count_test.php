<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.API.inc.php");
	 //header("Content-Type: text/javascript; charset=utf-8");
	 
	 require_once 'database_info.inc.php';
	 $api = new PrivateAPI($host, $database, $table, $username, $password);

	 $get_array = Database::clean($_GET); //clean the $_GET array
 	 //$query = "SELECT API_hit_date FROM " . Database::$table . " WHERE API_key = '" . $get_array['key'] . "' LIMIT 1";
 	 //$result = Database::get_all_results($query);
 	 //$result = $result[0];
 	 //if($api->update_API_hits($get_array['key'], $result['API_hit_date']) === false) echo "limit reached";	
	 $data = $api->get_json_from_assoc($get_array); //return user JSON objs based on API get params 
	 Database::close_connection();
	 echo $data;
?>