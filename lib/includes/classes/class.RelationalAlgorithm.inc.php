<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.DistanceCalculator.inc.php");

class RelationalAlgorithm{
	
	protected $numb_related_users_per_page = 10;
	protected $numb_of_each_column_for_algorithm;
	protected $nearby_users_radius_in_miles = 1000;
	protected $columns_for_algorithm = "tags, media, lat, lon"; //columns that the algorithm uses
	// protected $columns_for_algorithm_array;

	protected $users_tags;
	protected $users_media;
	protected $users_lat_lon;

	protected $api;

	public function __construct(){
		$this->api = new PrivateAPI();
		// $this->columns_for_algorithm_array = explode(", ", $this->columns_for_algorithm);
		$this->numb_of_each_column_for_algorithm = floor($this->numb_related_users_per_page/3);
	}

	//implements our sites algorithm to return related user JSON objs
	//algorithm displays related users by providing a more or less equivalent number of the most related users
	//in the media, tags, and location columns. If certain related users are present in more than one of these three
	//columns they are prioritized and have a greater chance of being included in the final 10 users returned

	//returns a string of 10 most related users as an array of JSON objects wrapped in a related_users obj
	public function get_related_users($user_id){
		$all_related_users_obj = json_decode($this->get_related_users_raw_JSON($user_id)); 
		if(!is_object($all_related_users_obj)) echo "NOT AN OBJECT"; 
		$most_media_ids = $this->get_most_occuring_by_array_of_ids($all_related_users_obj->media, $this->numb_of_each_column_for_algorithm);
		$most_tags_ids = $this->get_most_occuring_by_array_of_ids($all_related_users_obj->tags, $this->numb_of_each_column_for_algorithm);
		$most_location_ids = $this->get_most_occuring_by_array_of_ids($all_related_users_obj->location, $this->numb_of_each_column_for_algorithm);
		return $this->get_related_users_using_arrays_of_ids($most_media_ids, $most_tags_ids, $most_location_ids);
	}

	//combines the three arrays of most related users from the three columns included in the algorithm 
	//(media, tags, location) and evenly distributes them into one array of MOST related users ids.
	//Method then re-queries to return only those objects as a related_users JSON string
	protected function get_related_users_using_arrays_of_ids($array_1, $array_2, $array_3){
		$max_array_size = max(sizeof($array_1), sizeof($array_2), sizeof($array_3));
		$numb_users_chosen = 0;
		$numb_users_needed = $this->numb_related_users_per_page;
		$most_related_users_ids = array(); //holds user ids of the most related users
		$ids_already_added = array(); //keeps track of the ids of users that have already been added to $most_related_users_ids
		$i = 0;
		//distribute the contents from the media, tags, and locations obj fairly evenly
		while($numb_users_chosen < $numb_users_needed){
			//only keep searching if there are going to be any more possible results, else break
			if($i < $max_array_size){
				//add user id to the array if the array has enough elements and the current element being 
				//looped through hasnt already been added
				if($i < sizeof($array_1) && array_search($array_1[$i], $ids_already_added) == false){
					$most_related_users_ids[] = $array_1[$i];
					$numb_users_chosen++;
				}
				//dido
				if($i < sizeof($array_2) && !array_search($array_2[$i], $ids_already_added) == false){
					$most_related_users_ids[] = $array_2[$i];
					$numb_users_chosen++;
				} 
				//ibid
				if($i < sizeof($array_3) && !array_search($array_3[$i], $ids_already_added) == false){
				 $most_related_users_ids[] = $array_3[$i];
				 $numb_users_chosen++;
				}
			}
			else break;
			$i++;	
		}
		//query to get the MOST related users
		$query = "SELECT " . $this->api->public_columns_to_provide . " FROM " . Database::$table . " WHERE ";
		$j = 0;
		foreach($most_related_users_ids as $id){
			$query .= "id = '" . $id . "' ";
			if($j != sizeof($most_related_users_ids) -1) $query .= "OR ";
			$j++;
		}
		$query .= "ORDER BY likes DESC LIMIT " . sizeof($most_related_users_ids);
		//return a string of JSON using that wraps an array of user objects in a related_users object
		return $this->api->query_results_as_array_of_JSON_objs($query, "data", true);
	}

	//returns an array of user ids of the most repeated most relevant users in the column
	protected function get_most_occuring_by_array_of_ids($all_related_users_objs_array, $numb_to_return){
		$id_array = array();
		foreach ($all_related_users_objs_array as $raw_related_user) {
			//if there is not an error add the id to the array of ids
			if(!isset($raw_related_user->error)) $id_array[] = $raw_related_user->id; 
		}
		$most_repeated_array = array_count_values($id_array); //count the numb times each id appears
		arsort($most_repeated_array, SORT_REGULAR); //sort so that highest repeats are first
		$most_repeated_array = array_keys($most_repeated_array); //regular index array now holds the values of the related users ordered by most repeated
		// var_dump($most_repeated_array);
		// echo "<br/><br/>";
		return $most_repeated_array;
	}

	//looks up the user the algorithm is being executed for's media, tags, and location and breaks them up.
	//This initializes the low level arrays that the algorithm draws from
	protected function init_user_column_vars($user_id){
		$query = "SELECT " . $this->columns_for_algorithm . " FROM " . Database::$table . " WHERE id = '" . $user_id . "' LIMIT 1";
		//echo $query;
		$result = Database::get_all_results($query); //this actually only returns a 1D array of column key => vals
		//var_dump($result);
		$this->users_tags = explode(", ", $result['tags']);
		$this->users_media = explode(", ", $result['media']);
		$this->users_lat_lon = array($result['lat'], $result['lon']);
	}

	//returns JSON string of the 3 JSON objects each containing arrays of ALL related users for EACH tag or media 
	//that was similar to the user the algorithm is being run on
	protected function get_related_users_raw_JSON($user_id){
		$this->init_user_column_vars($user_id); //fill arrays from users algorithm columns
		$total_JSON_objs = "{";
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_tags, 'tags');
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_media, 'media');
		$total_JSON_objs .= $this->get_JSON_from_all_contents($user_id, $this->users_lat_lon, 'location');
		$total_JSON_objs = rtrim($total_JSON_objs, ","); //remove last comma
		$total_JSON_objs .= "}";
		return iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($total_JSON_objs));
	}

	//query and return a concatonated string of all JSON objs for each item in the arrays provided (arrays contain split media and tags columns)
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
			$JSON_output_string .= $this->api->query_results_as_array_of_JSON_objs($query); //notice: the string of json objs are not wrapped
			$JSON_output_string .= ",";
			if($column_name == "location") break; //do not loop again because location always returns the same results
		}
		$JSON_output_string = rtrim($JSON_output_string, ","); //remove last comma 
		$JSON_output_string .= '],';
		return $JSON_output_string;
	}
}
?>