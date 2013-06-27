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

	public static function commas_to_tags($string) {
		$output = explode(", ", $string);
		return $output;
	}

	//confirm signed in before calling this
	public function output_profile($user_id){
		$search_array = array('id' => $user_id,
							  'limit' => 1);
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	public function output_search_results($search_string, $numb_results, $page=1){
		$search_array = array('search' => $search_string,
							  'limit' => $numb_results,
							  'page' => $page);
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	public function output_highest_liked_users($numb_results) {
		$search_array = array('order_by' => 'likes',
							  'limit' => $numb_results,
							  'flow' => 'DESC');
		$search_obj = json_decode($this->api->get_JSON_from_GET($search_array));
		return $search_obj;
	}

	public function output_related_users($user_id, $numb_results){
		return $this->output_highest_liked_users(10);
	}

	#etc...
}
?>