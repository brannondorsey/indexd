<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");
	 require_once("classes/class.ContentOutput.inc.php");

	 Database::init_connection();
	 Session::start();
	 $c_out = new ContentOutput();
	 $c_out->output_search_results($_GET['search'], 10, $_GET['page']);
	 Database::close_connection();
?> 