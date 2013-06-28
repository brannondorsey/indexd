<?php
	 require_once("classes/class.PrivateAPI.inc.php");
	 require_once("classes/class.User.inc.php");
	 require_once("classes/class.Session.inc.php");

	 Database::init_connection();
	 Session::start();
	 $api = new PrivateAPI();
	 $user = new User();
	 //THIS WOULD ALL HAPPEN IN THE USERS REGISTER METHOD
	 //$IU = new InsertUpdate();
	 // $example_post_array = array(
	 // 	"email" => "brannon@brannondorsey.com",
	 // 	"password" => "glassjar",
	 // 	"first_name" => "Brannon",
	 // 	"last_name" => "Dorsey",
	 // 	"url" => "brannondorsey.com",
	 // 	"description" => "description",
	 // 	"media" => "sculpture, photography, creative code",
	 // 	"tags" => "saic, chicago, richmond, young arts",
	 // 	"zip" => "60601",
	 // 	"country" => "us"
	 // 	);
	 if($_POST){
		 $post_array = Database::clean($_POST);
		 $post_array['country'] = "us"; //add country manually for now
		 $user->register($post_array);
	}
	 Database::close_connection();
?> 