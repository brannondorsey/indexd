<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 Database::init_connection();
	 $algorithm = new RelationalAlgorithm();
	 $get_array = Database::clean($_GET);
	 $algorithm->get_related_users((int) $get_array['id']);
	 Database::close_connection();
	 //echo $data;
?>