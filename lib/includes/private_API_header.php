<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 Database::init_connection();
	 $algorithm = new RelationalAlgorithm();
	 $algorithm->get_related_users((int) $_GET['id']);
	 //$algorithm_obj = json_decode($data);
	 Database::close_connection();
	 //echo $data;
?>