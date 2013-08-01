<?php
require_once("class.Database.inc.php");
require_once("class.ContentOutput.inc.php");
//class to form and execute MySQL insert and update statements
class OrganizationAutocomplete {

	//returns JSON of all organizations that match the list of chars wrapped in a data object array
	public function get_results_as_JSON($chars){
		$query = "SELECT organization FROM " . Database::$organization_table . " WHERE organization LIKE '" . $chars . "%' ORDER BY organization";
		//if there are results for the current characters requested
		if($matching_organizations = Database::get_results_as_numerical_array($query, "organization")){
			$obj = new stdClass();
			$obj->data = $matching_organizations;
			return json_encode($obj);
		}else return "{ \"error\" : \"no results found\"}";
	}

	//adds the contents of a comma delimited organizations list to the organizations table
	//returns false on failure
	public function add_list_to_organization_table($organizations_list){
		$query = "SELECT organization FROM " . Database::$organization_table;
		if($organization_list = Databse::get_results_as_numerical_array($query, "organization")){
			$organizations = ContentOutput::commas_to_list($organizations_list);
			foreach($organizations as $organization){
				if(!in_array($organization, $organization_list)) $this->add_organization($organization);
			}
		}else{ //if there is nothing in the organizations table
			$organizations = ContentOutput::commas_to_list($organizations_list);
			foreach($organizations as $organization){
				$this->add_organization($organization);
			}
		}
	}

	//adds an organization to the organization table.
	//returns true on success and false on failure.
	protected function add_organization($organization){
		$query = "INSERT INTO " . Database::$organization_table . " (`organization`) VALUES ('" . $organization . "')";
		return Database::execute_sql($query);
	}
	
}
?>