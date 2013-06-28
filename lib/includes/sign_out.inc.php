<?php
	require_once('classes/class.Session.inc.php');
	Session::start();
	Session::destroy();
	header('Location : index.php')
?>