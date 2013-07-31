<?php
require_once("class.Database.inc.php");
require_once("class.API.inc.php");

//class to form and execute MySQL insert and update statements
class InsertUpdate {

	public function __construct(){
		
	}
	//handles dynamic formation of INSERT and UPDATE queries from $_POST and executes them
	//post array should be cleaned before using this function
	public function execute_from_assoc($post_array, $statement_type="INSERT", $set_statement=NULL){
		$statement_type = strtoupper($statement_type);
		if($statement_type == "INSERT"){
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
		}
		//if statement type is UPDATE, the id of the row to update was specified in the $post_array,
		//and what to update (set) was specified
		else if($statement_type == "UPDATE" &&
				isset($post_array['id']) &&
			    $set_statement != NULL){
			$query = $statement_type . " " . Database::$table . " SET " . $set_statement . " = '" . $post_array[$set_statement]
			. "' WHERE id = '" . $post_array['id'] . "' LIMIT 1";
		}
		//if the previous wasn't true but it was still and update statement
		else if($statement_type == "UPDATE" &&
			    isset($post_array['id'])){
			$id = $post_array['id'];
			unset($post_array['id']);
			$query = $statement_type . " " . Database::$table . " SET ";
			foreach ($post_array as $column_name => $column_value) {
				$query .= $column_name . " = '" . $column_value . "', ";
			}
			$query = rtrim($query, ", ");
			$query .= " WHERE id = '" . $id . "' LIMIT 1";
			//echo $query;
		}
		else{
			echo "incorrect parameters passed to InsertUpdate::execute_from_assoc()";
		 	return false;
		}
		//echo $query;
		return Database::execute_sql($query);
	}

	//increments an int column value 
	public function increment($user_id, $column_name){
		$query = "UPDATE " . Database::$table . " SET " . $column_name . "=" . $column_name . "+1 WHERE id='" . $user_id . "' LIMIT 1";
		return Database::execute_sql($query);
	}
}
?>