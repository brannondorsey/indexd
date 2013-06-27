<?php
require_once("class.Database.inc.php");

class API {

	public $public_columns_to_provide;

	protected $columns_to_provide;
	protected $default_output_limit = 25;
	protected $max_output_limit = 250;
	protected $default_order_by = "ORDER BY last_name ";
	protected $default_flow = "ASC ";
	protected $JSON_string;
	protected $full_text_columns;
	protected $API_key;

	
	public function __construct(){
		$this->columns_to_provide = 
			"id, first_name, last_name, url, email, city, state, country, zip, lat, lon, datetime_joined, description, media, tags, likes";
		$this->full_text_columns = "first_name, last_name, email, url, description, media, tags, city, state, country";
		$this->public_columns_to_provide = $this->columns_to_provide;
	}

	//Returns a valid JSON string from $_GET values. Array must be sanitized before using this function.
	public function get_JSON_from_GET(&$get_array, $object_parent_name="data"){
		$query = $this->form_query($get_array);
		// echo $query;
		// echo "<br/><br/>";
		if($this->check_API_key()) $this->JSON_string = $this->query_results_as_array_of_JSON_objs($query, $object_parent_name, true);
		else $this->JSON_string = $this->get_error("API key is invalid or was not provided");
		return $this->JSON_string;
	}


	public function query_results_as_array_of_JSON_objs($query, $object_parent_name=NULL, $b_wrap_as_obj=false){
		$JSON_output_string = "";
		//if there were results output them as a JSON data obj
		//echo "the parent name is " . $object_parent_name . " and the boolean is " . $b_wrap_as_obj;
		if($results_array = Database::get_all_results($query)){
				//if the objects being output should be wrapped in an object specified by the parameters of this function
				if($object_parent_name != NULL && $b_wrap_as_obj){
				 	$JSON_output_string = '{"' . $object_parent_name . '":[';
				 }
				$JSON_output_string .= $this->output_objects($results_array);
				//see above
				if($object_parent_name != NULL && $b_wrap_as_obj){
					$JSON_output_string .= ']}';
				}
			}
		//if no results were found return a JSON error obj
		else $JSON_output_string = $this->get_error("no results found");
		return iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($JSON_output_string));
	}

	//outputs JSON object from 1D or 2D MySQL results array
	protected function output_objects($mysql_results_array){
		$JSON_output_string = "";
		$count_only_key = "count"; //change default COUNT(*) key name
		if(isset($mysql_results_array[0])){
			$i = 0;
			foreach ($mysql_results_array as $user_row) {
				$JSON_output_string .= "{";
				$j = 0;
				foreach($user_row as $key => $value){
					if($key == "COUNT(*)") $key = $count_only_key;
					$JSON_output_string .= '"' . $key . '"' . ':';
					$JSON_output_string .= '"' . $value . '"';
					if ($j != sizeof($user_row) -1) $JSON_output_string .= ',';
					$j++;
				}
				$JSON_output_string .= "}";
				if ($i != sizeof($mysql_results_array) -1) $JSON_output_string .= ',';
				$i++;
			}
		}
		else{
			$user_row = $mysql_results_array;
			$JSON_output_string .= "{";
				$j = 0;
				foreach($user_row as $key => $value){
					if($key == "COUNT(*)") $key = $count_only_key;
					$JSON_output_string .= '"' . $key . '"' . ':';
					$JSON_output_string .= '"' . $value . '"';
					if ($j != sizeof($user_row) -1) $JSON_output_string .= ',';
					$j++;
				}
				$JSON_output_string .= "}";
		}
		return $JSON_output_string;
	}

	//outputs JSON error object with error message argument
	protected function get_error($error_message){
		return "{\"error\": \"$error_message\"}";
	}

	//builds a dynamic MySQL query statement from a $_GET array. Array must be sanitized before using this function.
	protected function form_query(&$get_array){

		$column_parameters = array();
		$columns_to_provide_array = explode(', ', $this->columns_to_provide);
		$search = "";
		$order_by = "";
		$flow = "";
		$limit = "";
		$page = 1;
		$exact = false;
		$count_only = false;
		$this->API_key = "";

		//distribute $_GETs to their appropriate arrays/vars
		foreach($get_array as $parameter => $value){
			if($this->is_column_parameter($parameter, $columns_to_provide_array)){ 
				$column_parameters[$parameter] = $value;
			}
			else if($parameter == 'search') $search = $value;
			else if($parameter =='order_by') $order_by = $value;
			else if($parameter == 'flow') $flow = $value;
			else if($parameter == 'limit') $limit = $value;
			else if($parameter == 'page') $page = (int) $value;
			else if($parameter == 'exact' &&
				    strtolower($value) == "true") $exact = true;
			else if($parameter == 'count_only' &&
				    strtolower($value) == "true" ||
				    $value = true){
				$count_only = true;
			} 
			else if($parameter == 'key') $this->API_key = $value; 
		}

		$match_against_statement = 'MATCH (' . $this->full_text_columns . ') AGAINST (\'' . $search . '\' IN BOOLEAN MODE) ';
		if($count_only) $query = "SELECT COUNT(*)";
		else $query = "SELECT " . $this->columns_to_provide;
		if($search != "") $query .= ", " . $match_against_statement . "AS score";
		$query .= " FROM "  . Database::$table ." ";

		//if search was a parameter overide column paramters and use MATCH...AGAINST
		if($search != ""){
			$this->append_prepend($search, "'");
			$query .= "WHERE $match_against_statement ORDER BY score DESC ";
			// echo $query;
		}
		//if search was not used use LIKE
		else{
			//add WHERE statements
			if(sizeof($column_parameters) > 0){
				$i = 0;
				$query .= "WHERE ";
				foreach ($column_parameters as $parameter => $value) {
					//if exact parameter was specified as TRUE 
					//or column parameter is id search by = not LIKE
					if($parameter == 'id' || $exact){
						$this->append_prepend($value, "'");
					 	$query .= "$parameter = $value ";
					}
					else $query .= "$parameter LIKE '%$value%' ";
					if($i != sizeof($column_parameters) -1) $query .= "AND ";
					$i++;
				}
			}
		
			//add ORDER BY statement
			$order_by_string;
			if($order_by != "" &&
			$this->is_column_parameter($order_by, $columns_to_provide_array)){
				$order_by_string = "ORDER BY $order_by ";
			}
			else $order_by_string = $this->default_order_by;
			$query .= $order_by_string;

			//add FLOW statement
			$flow_string;
			$flow = strtoupper($flow);
			if($flow != "" &&
			$flow == 'ASC' ||
			$flow == 'DESC'){
				$flow_string = "$flow ";
			}
			else $flow_string = $this->default_flow;
			$query .= $flow_string;
		}
		//only add LIMIT of it is not a COUNT query
		if(!$count_only){
			//add LIMIT statement
			$limit_string;
			if($limit != ""){
				$limit = (int) $limit;
				if((int) $limit > $this->max_output_limit) $limit = $this->max_output_limit;
				if((int) $limit < 1) $limit = 1;
				$limit_string = "LIMIT $limit";
			} 
			else{
				$limit = $this->default_output_limit;
				$limit_string = "LIMIT $limit";	
			} 
			$query .= $limit_string;
		}

		//add PAGE statement
		if($page != "" &&
			$page > 1){
			$query .= " OFFSET " . $limit * ($page -1);
		}

		//echo $query . "<br/>";
		return $query;
	}

	protected function check_API_key(){
		$API_key_query = "SELECT id FROM " . Database::$table . " WHERE API_key='" . $this->API_key ."' LIMIT 1";
		//if the key was provided and it is the right length test it
		if($this->API_key != "" &&
			strlen($this->API_key) == 40){
			$results = Database::get_all_results($API_key_query);
			if($results &&
				count($results) > 0){
				//add insert SQL statement here to keep track of api hits 
			 return true;
			}
			else return false;
		}
		//if the api key wasnt provided or isn't the right length return before querying
		else return false;
	}

//------------------------------------------------------------------------------
//HELPERS

	//appends and prepends slashes to string for WHERE statement values
	protected function append_prepend(&$string, $char){
		$string = $char . $string . $char;
	}

	//checks if a parameter string is also the name of a SELECT statement's requested column
	protected function is_column_parameter($parameter_name, $columns_to_provide_array){
		return in_array ($parameter_name, $columns_to_provide_array);
	}

}

?>