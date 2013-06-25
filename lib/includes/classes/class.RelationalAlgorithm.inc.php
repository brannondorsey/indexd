<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");

class RelationalAlgorithm{
	
	protected $numb_related_users_per_page = 10;
	protected $columns_for_algorithm = "tags, media, lat_lon";
	protected $columns_for_algorithm_array;

	protected $users_tags;
	protected $users_media;
	protected $users_lat_lon;

	protected $api;

	public function __construct(){
		$this->api = new PrivateAPI();
		$this->columns_for_algorithm_array = explode(", ", $this->columns_for_algorithm);
	}

	protected function init_user_column_vars($user_id){
		$query = "SELECT " . $this->columns_for_algorithm . " FROM " . Database::$table . " WHERE id = '" . $user_id . "' LIMIT 1";
		//echo $query;
		$result = Database::get_all_results($query); //this actually only returns a 1D array of column key => vals
		//var_dump($result);
		$this->users_tags = explode(", ", $result['tags']);
		$this->users_media = explode(",", $result['media']);
		$this->users_lat_lon = explode(", ", $result['lat_lon']);
	}

	//implements our sites algorithm to return related user JSON objs
	public function get_related_users($user_id){
		$this->init_user_column_vars($user_id);
		$total_JSON_objs = "{";
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_tags, 'tags');
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_media, 'media');
		//$total_JSON_objs .= $this->get_JSON_from_all_contents($this->users_lat_lon, 'lat_lon');
		//DO SOMETHING ELSE WITH THIS ^ INVOLVING DISTANCE PROXIMATEY
		$total_JSON_objs = rtrim($total_JSON_objs, ","); //remove last comma
		$total_JSON_objs .= "}";
		return $total_JSON_objs;
	}

	protected function get_JSON_from_all_contents($user_id, $array, $column_name){
		$JSON_output_string = '"' . $column_name . '":[';
		//$i = 0;
		//$array contains vals to query by
		foreach($array as $new_query_value){
			echo "<br/><br/>" . var_dump($new_query_value);
			$query = "SELECT " . $this->api->public_columns_to_provide . " FROM " . Database::$table . " WHERE " 
			. $column_name . " = '" . $new_query_value . "' AND id != '" . $user_id . "' ORDER BY likes LIMIT " . $this->numb_related_users_per_page;
			$JSON_output_string .= $this->api->query_results_as_array_of_JSON_objs($query);
			$JSON_output_string .= ","; 
		}
		$JSON_output_string = rtrim($JSON_output_string, ","); //remove last comma 
		$JSON_output_string .= '],';
		return $JSON_output_string;
	}
}
?>