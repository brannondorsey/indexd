<?php
	require_once("../lib/includes/classes/class.API.inc.php");
	 $api = new API();
	 $api->get_JSON_from_GET($_GET);
?>