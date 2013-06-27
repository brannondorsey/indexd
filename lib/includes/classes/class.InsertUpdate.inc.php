<?php
require_once("class.Database.inc.php");
require_once("class.API.inc.php");

//class to form and execute MySQL insert and update statements
class InsertUpdate {

	public function __construct(){
		
	}
	//handles dynamic formation of INSERT and UPDATE queries from $_POST and executes them
	//post array should be cleaned before using this function
	public function execute_from_assoc($post_array, $statement_type="INSERT"){
		$statement_type = strtoupper($statement_type);
		$query = $statement_type . " INTO " . Database::$table . " ("; 
		foreach($post_array as $key => $value){
			$query .= " `" . $key . "`,";
		}
		$query = rtrim($query, ",");
		$query .= ") VALUES (";
		foreach($post_array as $key => $value){
			//if($key == 'lat' || $key == 'lon' || $value == 0) $query .= " " . $value . ",";
			$query .= " '" . $value . "',";
		}
		$query = rtrim($query, ",");
		$query .= ");";
		echo $query;
		Database::execute_sql($query);
	}
}
?>