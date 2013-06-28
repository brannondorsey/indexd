<?php

class Database {

	public static $table    = "users";
	public static $root_dir_link = "http://localhost:8888/";

	protected static $user     = "root";
	protected static $password = "root";
	protected static $db       = "AWU";
	protected static $host     = "localhost";
	protected static $mysqli;

	//initialize the database connection
	public static function init_connection(){
		self::$mysqli = new mysqli(self::$host, self::$user, self::$password, self::$db);
	}

	//close the database connection
	public static function close_connection(){
		self::$mysqli->close();
	}
	
	//execute sql query statement. Used for INSERT and UPDATE mostly. Returns false if query fails
	public static function execute_sql($query) {
		if(self::$mysqli->query($query)) return true;
		else echo self::$mysqli->error;
		return false;
	}
	
	//returns array of one result row if one result was found or 2D array of all returned rows if multiple were found
	public static function get_all_results($query) {
		$result_to_return = array(); //maybe this shouldnt be like this...
		if ($result = self::$mysqli->query($query)) {
				$i=0;
				while ($row = $result->fetch_assoc()) {
					$result_to_return[$i] = $row;
					$i++;	
				}
			if (count($result_to_return) > 1) {
				return $result_to_return;
			} 
			else if(count($result_to_return) == 1) {
				return $result_to_return[0];
			} 
			else return false; //there were no results found
		}
		else echo " MYSQL QUERY FAILED";
	}

	//returns string or assosciative array of strings
	//mainly for $_POST and $_GET
	public static function clean($string){
		if(isset($string) && !empty($string)){
			$new_string_array;
			//if the string is actually an assoc array
			if(is_array($string)){
				foreach($string as $string_array_key => $string_array_value){
					if($string_array_key == 'media' ||
					   $string_array_key == 'tags') $string_array_value = self::format_list_for_db($string_array_value);
					if($string_array_key == 'email') $string_array_value = strtolower($string_array_value);
					//$string_array_value = self::clean_string($string_array_value);
					$new_string_array[$string_array_key] = $string_array_value;
				}
				$string = $new_string_array;
			}
			//else just clean it
			//else $string = self::clean_string($string, self::$mysqli);
			return $string;
		}
		else return false; //nothing was passed as an argument
	}

//------------------------------------------------------------------------------
//HELPERS

	//series of cleans to be perfomed on one string
	protected static function clean_string($string){
		$string = htmlspecialchars($string);
		$string = self::$mysqli->real_escape_string($string);
		return $string;
	}

	//formats lists like media and tags from POST to be comma-space delimited per our sites standard 
	//called inside clean()
	protected static function format_list_for_db($string){
		$string = strtolower($string);
		$array = explode(",", $string);
		$new_array = array();
		foreach($array as $value){
			$new_array[] = trim($value);
		}
		$string = implode(", ", $new_array);
		return $string;
	}
}

?>