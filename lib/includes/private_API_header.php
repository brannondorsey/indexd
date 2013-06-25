<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.RelationalAlgorithm.inc.php");
	 Database::init_connection();
	 $algorithm = new RelationalAlgorithm();
	 $data = $algorithm->get_related_users((int) $_GET['id']);
	 Database::close_connection();
	 echo $data;
?>