<?php
require_once("class.Database.inc.php");
require_once("class.ContentOutput.inc.php");
//class to form and execute MySQL insert and update statements
class OrganizationAutocomplete {

	public function __construct(){
		
	}

	//returns JSON of all organizations that match the list of chars wrapped in a data object array
	public function get_results_as_JSON($chars){
		$query = "SELECT organization FROM " . Database::$organization_table . " WHERE organization LIKE '" . $chars . "%'";
		//if there are results for the current characters requested
		if($matching_organizations = $this->get_results_as_numerical_array($query)){
			// //if there is only one result wrap it in another array
			// if(!isset($results[0])) $results = array($results);
			// $matching_organizations = array();
			// foreach($results as $result_row){
			// 	$matching_organizations[] = $result_row['organization'];
			// }
			//var_dump($matching_organizations);
			$obj = new stdClass();
			$obj->data = $matching_organizations;
			return json_encode($obj);
		}else return "{ error : \"no results found\"}";
	}

	//adds the contents of a comma delimited organizations list to the organizations table
	//returns false on failure
	public function add_list_to_organization_table($organizations_list){
		$query = "SELECT organization FROM " . Database::$organization_table;
		if($organization_list = $this->get_results_as_numerical_array($query)){
			$organizations = ContentOutput::commas_to_list($organizations_list);
			foreach($organizations as $organization){
				if(!in_array($organization, $organization_list)) $this->add_organization($organization);
			}
		}else return false;
	}

	//adds an organization to the organization table.
	//returns true on success and false on failure.
	protected function add_organization($organization){
		$query = "INSERT INTO " . Database::$organizations_list . " (`organization`) VALUES ('" . $organization . "')";
		return Database::execute_from_assoc($query);
	}

	//takes a MySQL query and returns a 1D indexd array of results.
	//i.e. it can only be used with SELECT statement that returns results of only one column
	//returns array on success and false on failure.
	//called from add_list_to_organization_table() and get_results_as_JSON()
	protected function get_results_as_numerical_array($query){
		if($results = Database::get_all_results($query)){
			if(!isset($results[0])) $results = array($results); //wraps in array if $result is 1D
			$numerical_array = array();
			foreach($results as $result_row){
				$numerical_array[] = $result_row['organization'];
			}
			return $numerical_array;
		}else return false;
	}
	
}
?>