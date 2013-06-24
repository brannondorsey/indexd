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

	
	public function __construct(){
		$this->db = new Database();
		$this->columns_to_provide = 
			"id, first_name, last_name, url, email, city, state, country, datetime_joined, description, media, tags";
	}

	//Returns valid JSON from $_GET values. Array must be sanitized before using this function.
	public function echo_JSON_from_GET(&$get_array){
		$query = $this->form_query($get_array);
		//if there were results output them as a JSON data obj
		if($results_array = $this->db->get_all_results($query)){
			echo "<br/><br/>";
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
		$query = "SELECT " . $this->columns_to_provide . " FROM "  . $this->db->table ." ";
		$column_parameters = array();
		$columns_to_provide_array = explode(', ', $this->columns_to_provide);
		$limit = "";
		$order_by = "";
		$flow = "";

		//distribute $_GETs to their appropriate arrays/vars
		foreach($get_array as $parameter => $value){
			if($this->is_column_parameter($parameter, $columns_to_provide_array)){ 
				$column_parameters[$parameter] = $value;
			}
			else if($parameter == 'limit') $limit = $value;
			else if($parameter =='order_by') $order_by = $value;
			else if($parameter == 'flow') $flow = $value;
		}

		//add WHERE statements
		if(sizeof($column_parameters) > 0){
			$i = 0;
			$query .= "WHERE ";
			foreach ($column_parameters as $parameter => $value) {
				$this->add_single_quotes($value);
				$query .= "$parameter=$value ";
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
	protected function add_single_quotes(&$string){
		$string = "'" . $string . "'";
	}

	//checks if a parameter string is also the name of a SELECT statement's requested column
	protected function is_column_parameter($parameter_name, $columns_to_provide_array){
		return in_array ($parameter_name, $columns_to_provide_array);
	}

}

?>