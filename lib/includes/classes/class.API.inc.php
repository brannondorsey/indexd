<?php
require_once("class.Database.inc.php");

class API {

	protected $db;
	protected $columns_to_provide;
	protected $default_output_limit = 25;
	protected $max_output_limit = 250;
	protected $default_order_by = "ORDER BY last_name ";
	protected $default_flow = "DESC ";

	
	public function __construct(){
		$this->db = new Database();
		$this->columns_to_provide = 
			"id, first_name, last_name, url, email, city, state, country, datetime_joined, description, media, tags";
	}

	public function get_JSON_from_GET(&$get_array){
		$query = $this->form_query($get_array);
		echo $query;
	}	

	public function form_query(&$get_array){
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
			foreach ($column_parameters as $parameter => $value) {
				$query = $query . "WHERE $parameter =$value ";
				if($i != sizeof($column_parameters) -1) $query = $query . "AND ";
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
		$query = $query . $order_by_string;

		//add FLOW statement
		$flow_string;
		$flow = strtoupper($flow);
		if($flow != "" &&
		$flow == 'ASC' ||
		$flow == 'DESC'){
			$flow_string = "$flow ";
		}
		else $flow_string = $this->default_flow;
		$query = $query . $flow_string;

		//add LIMIT statement
		$limit_string;
		if($limit != ""){
			if((int) $limit > $this->max_output_limit) $limit = $this->max_output_limit;
			if((int) $limit < 1) $limit = 1;
			$limit_string = "LIMIT $limit";
		} 
		else $limit_string = "LIMIT $this->default_output_limit";
		$query = $query . $limit_string;
		return $query;
	}

	protected function is_column_parameter($parameter_name, $columns_to_provide_array){
		return in_array ($parameter_name, $columns_to_provide_array);
	}

}

?>