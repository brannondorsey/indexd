<?php
require_once("class.Database.inc.php");

class API {

	public $db;
	protected $columns_to_provide;
	protected $default_output_limit = 25;
	protected $max_output_limit = 250;
	protected $default_order_by = "ORDER BY last_name ";
	protected $default_flow = "ASC ";
	protected $JSON_string;
	protected $full_text_columns;

	
	public function __construct(){
		$this->db = new Database();
		$this->columns_to_provide = 
			"id, first_name, last_name, url, email, city, state, country, zip, lat_lon, datetime_joined, description, media, tags, likes";
		$this->full_text_columns = "first_name, last_name, email, url, description, media, tags, city, state, country";
	}

	//Returns valid JSON from $_GET values. Array must be sanitized before using this function.
	public function echo_JSON_from_GET(&$get_array){
		$query = $this->form_query($get_array);
		echo $query;
		echo "<br/><br/>";
		//if there were results output them as a JSON data obj
		if($results_array = $this->db->get_all_results($query)){
			$this->JSON_string .= '{"data":[';
			$this->output_objects($results_array);
			$this->JSON_string .= ']}';
		}
		//if no results were found return a JSON error obj
		else $this->output_error("no results found");
		echo $this->JSON_string;
	}

	//outputs JSON object from 1D or 2D MySQL results array
	protected function output_objects($results_array){
		if(isset($results_array[0])){
			$i = 0;
			foreach ($results_array as $user_row) {
				$this->JSON_string .= "{";
				$j = 0;
				foreach($user_row as $key => $value){
					$this->JSON_string .= '"' . $key . '"' . ':';
					$this->JSON_string .= '"' . $value . '"';
					if ($j != sizeof($user_row) -1) $this->JSON_string .= ',';
					$j++;
				}
				$this->JSON_string .= "}";
				if ($i != sizeof($results_array) -1) $this->JSON_string .= ',';
				$i++;
			}
		}
		else{
			$user_row = $results_array;
			$this->JSON_string .= "{";
				$j = 0;
				foreach($user_row as $key => $value){
					$this->JSON_string .= '"' . $key . '"' . ':';
					$this->JSON_string .= '"' . $value . '"';
					if ($j != sizeof($user_row) -1) $this->JSON_string .= ',';
					$j++;
				}
				$this->JSON_string .= "}";
		}
	}

	//outputs JSON error object with error message argument
	protected function output_error($error_message){
		$this->JSON_string .= "{\"error\": \"$error_message\"}";
	}

	//builds a dynamic MySQL query statement from a $_GET array. Array must be sanitized before using this function.
	protected function form_query(&$get_array){

		$column_parameters = array();
		$columns_to_provide_array = explode(', ', $this->columns_to_provide);
		$search = "";
		$order_by = "";
		$flow = "";
		$limit = "";
		$exact = false;

		//distribute $_GETs to their appropriate arrays/vars
		foreach($get_array as $parameter => $value){
			if($this->is_column_parameter($parameter, $columns_to_provide_array)){ 
				$column_parameters[$parameter] = $value;
			}
			else if($parameter == 'search') $search = $value;
			else if($parameter == 'limit') $limit = $value;
			else if($parameter =='order_by') $order_by = $value;
			else if($parameter == 'flow') $flow = $value;
			else if($parameter == 'exact'){
				if(strtolower($value) == "true") $exact = true;
			}
		}

		$match_against_statement = 'MATCH (' . $this->full_text_columns . ') AGAINST (\'' . $search . '\' IN BOOLEAN MODE) ';
		$query = "SELECT " . $this->columns_to_provide;
		if($search != "") $query .= ", " . $match_against_statement . "AS score";
		$query .= " FROM "  . $this->db->table ." ";

		//if search was a parameter overide column paramters and use MATCH...AGAINST
		if($search != ""){
			$this->append_prepend($search, "'");
			$query .= "WHERE $match_against_statement ORDER BY score DESC ";
		}
		//if search was not used use LIKE
		else{
			//add WHERE statements
			if(sizeof($column_parameters) > 0){
				$i = 0;
				$query .= "WHERE ";
				foreach ($column_parameters as $parameter => $value) {
					//if column parameter is id search by = not LIKE
					if($parameter == 'id' || $exact){
						$this->append_prepend($value, "'");
					 	$query .= "$parameter = $value";
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

		//add LIMIT statement
		$limit_string;
		if($limit != ""){
			$limit = (int) $limit;
			if((int) $limit > $this->max_output_limit) $limit = $this->max_output_limit;
			if((int) $limit < 1) $limit = 1;
			$limit_string = "LIMIT $limit";
		} 
		else $limit_string = "LIMIT $this->default_output_limit";
		$query .= $limit_string;
		return $query;
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