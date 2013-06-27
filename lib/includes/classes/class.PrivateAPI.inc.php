<?php
require_once("class.Database.inc.php");
require_once("class.API.inc.php");

class PrivateAPI extends API {
	public function __construct(){
		parent::__construct();
		$this->columns_to_provide = $this->columns_to_provide . ", password, API_key, API_hits, verified";
		$this->max_output_limit = 1000;
	}

	//bypass API key
	public function check_API_key(){
		return true;
	}

	// public function get_logged_in_user_obj($user_id){
	// 	$query_array = array('id' => $user_id, 
	// 						  'limit' => 1);
	// 	$JSON_obj = json_decode($this->get_JSON_from_get($query_array));
	// 	if(is_object($JSON_obj)) return $JSON_obj;
	// 	else return false;
	// }
}
?>