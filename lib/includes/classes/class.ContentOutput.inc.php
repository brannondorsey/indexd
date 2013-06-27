<?php
require_once("class.User.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.RelationalAlgorithm.inc.php");

class ContentOutput{
	protected $user;
	protected $api;
	protected $algorithm;
	public function __construct(){
		$user = new User();
		$this->api = new PrivateAPI();
		$algorithm = new RelationalAlgorithm();
	}

	//confirm signed in before calling this
	public function output_profile($user_id){
	}

	public function output_search_results($search_string, $numb_results, $page){
		$search_array = array('search' => $search_string,
							  'limit' => $numb_results,
							  'page' => $page);
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		echo $search_obj->data[0]->url;
	}

	public function output_related_users($user_id, $numb_results){

	}

	#etc...
}
?>