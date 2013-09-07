<?php
require_once("class.Database.inc.php");
require_once("class.API.inc.php");

class PrivateAPI extends API {

	public function __construct($host, $db, $table, $username, $password){
		parent::__construct($host, $db, $table, $username, $password);
	}

	//bypass API key
	public function check_API_key(){
		return true;
	}

	//returns the JSON obj (note: not json string) that represents all of the users public data 
	//unless private data optional param is true.
	public function get_logged_in_user_obj($user_id, $get_private_data=false){
		$query_array = array('id' => $user_id, 
							  'limit' => 1);
		//set the columns to provide to public so as not to store private data in session unless otherwise specified
		if(!$get_private_data) $this->columns_to_provide = $this->public_columns_to_provide; 
		$JSON_obj = json_decode($this->get_json_from_assoc($query_array));
		//reset columns to provide to private for the rest of the PrivateAPI class
		if(!$get_private_data) $this->columns_to_provide = $this->private_columns_to_provide;
		if(is_object($JSON_obj)) return $JSON_obj;
		else return false;
	}
}
?>