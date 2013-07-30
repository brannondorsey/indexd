<?php
	 require_once("../lib/includes/classes/class.Database.inc.php");
	 require_once("../lib/includes/classes/class.OrganizationAutocomplete.inc.php");
	 //header("Content-Type: text/javascript; charset=utf-8");
	 Database::init_connection();
	 $autocomplete = new OrganizationAutocomplete();
	 $get_array = Database::clean($_GET); //clean the $_GET array
	 if(isset($get_array['chars'])){
	 	$results_obj = $autocomplete->get_results_as_JSON($get_array['chars']);
	 }
	 echo $results_obj;
	 Database::close_connection();
	 
?>