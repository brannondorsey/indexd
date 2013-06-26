<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.DistanceCalculator.inc.php");

class RelationalAlgorithm{
	
	protected $numb_related_users_per_page = 10;
	protected $numb_of_each_column_for_algorithm;
	protected $nearby_users_radius_in_miles = 00;
	protected $columns_for_algorithm = "tags, media, lat, lon";
	protected $columns_for_algorithm_array;

	protected $users_tags;
	protected $users_media;
	protected $users_lat_lon;

	protected $api;

	public function __construct(){
		$this->api = new PrivateAPI();
		$this->columns_for_algorithm_array = explode(", ", $this->columns_for_algorithm);
		$this->numb_of_each_column_for_algorithm = floor($this->numb_related_users_per_page/3);
	}

	//implements our sites algorithm to return related user JSON objs
	public function get_related_users($user_id){
		//$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($this->get_related_users_raw_JSON($user_id)));
		$all_related_users_obj = json_decode($this->get_related_users_raw_JSON($user_id));
		if(is_object($all_related_users_obj)) echo $all_related_users_obj->tags[0]->first_name;
		else echo "NOT AN OBJECT";
		echo "<br/><br/>";
		//echo $this->get_related_users_raw_JSON($user_id);
		//var_dump($all_related_users_obj->media[0]);
		$this->get_most_occuring($all_related_users_obj->media, $this->numb_of_each_column_for_algorithm);
		$this->get_most_occuring($all_related_users_obj->tags, $this->numb_of_each_column_for_algorithm);
		$this->get_most_occuring($all_related_users_obj->location, $this->numb_of_each_column_for_algorithm);
		//echo $most_repeated_array[60];
	}

	protected function get_most_occuring($all_related_users_objs_array, $numb_to_return){
		$id_array = array();
		foreach ($all_related_users_objs_array as $raw_related_user) {
			if(!isset($raw_related_user->error)) $id_array[] = $raw_related_user->id;
		}
		$most_repeated_array = array_count_values($id_array);
		arsort($most_repeated_array, SORT_REGULAR);
		$most_repeated_array = array_keys($most_repeated_array);
		//$most_repeated_array = array_flip($most_repeated_array);
		//array_walk($most_repeated_array, array($this, 'array_walk_callback'));
		var_dump($most_repeated_array);
		echo "<br/><br/>";
	}

	protected function array_walk_callback($value){
		echo "the value is " . $value;
	}

	protected function init_user_column_vars($user_id){
		$query = "SELECT " . $this->columns_for_algorithm . " FROM " . Database::$table . " WHERE id = '" . $user_id . "' LIMIT 1";
		//echo $query;
		$result = Database::get_all_results($query); //this actually only returns a 1D array of column key => vals
		//var_dump($result);
		$this->users_tags = explode(", ", $result['tags']);
		$this->users_media = explode(", ", $result['media']);
		$this->users_lat_lon = array($result['lat'], $result['lon']);
	}

	//returns JSON
	protected function get_related_users_raw_JSON($user_id){
		$this->init_user_column_vars($user_id);
		$total_JSON_objs = "{";
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_tags, 'tags');
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_media, 'media');
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_lat_lon, 'location');
		$total_JSON_objs = rtrim($total_JSON_objs, ","); //remove last comma
		$total_JSON_objs .= "}";
		return iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($total_JSON_objs));
	}

	protected function get_JSON_from_all_contents($user_id, $array, $column_name){
		$JSON_output_string = '"' . $column_name . '":[';
		//$array contains vals to query by
		foreach($array as $new_query_value){
			$query = "SELECT " . $this->api->public_columns_to_provide . " FROM " . Database::$table . " WHERE ";
			//form query statement differently if the obj name will be location
			if($column_name == "location"){
				$location_range = DistanceCalculator::get_distance_range($this->users_lat_lon[0], $this->users_lat_lon[1], $this->nearby_users_radius_in_miles);
				$query .= "lat >= " . $location_range['min lat'] . " AND lat <= " . $location_range['max lat']
				 . " AND lon >= " . $location_range['min lon'] . " AND lon <= " . $location_range['max lon'];
			}
			else{
				$query .= $column_name . " LIKE '%" . $new_query_value . "%'";
			}
			$query .= " AND id != '" . $user_id . "' ORDER BY likes LIMIT " . $this->numb_related_users_per_page;
			$JSON_output_string .= $this->api->query_results_as_array_of_JSON_objs($query);
			$JSON_output_string .= ",";
			if($column_name == "location") break; //do not loop again because location always returns the same results
		}
		$JSON_output_string = rtrim($JSON_output_string, ","); //remove last comma 
		$JSON_output_string .= '],';
		return $JSON_output_string;
	}
}
?>