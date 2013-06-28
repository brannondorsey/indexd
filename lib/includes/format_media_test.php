<?php
	require_once 'classes/class.Database.inc.php';
	$get = "test,   THIS as,            if   sdfsd , it were, a , tag";
	var_dump($get);
	$get = Database::format_for_db($get);
	var_dump($get);

?>