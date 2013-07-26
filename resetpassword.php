<?php
	 require_once("lib/includes/classes/class.PrivateAPI.inc.php");
	 require_once("lib/includes/classes/class.User.inc.php");
	 require_once("lib/includes/classes/class.Session.inc.php");
	 require_once("lib/includes/classes/class.ContentOutput.inc.php");

	 //note that this user object doesn't actually represent a specific user in this page.
	 //It is used to give access to its reset password function.
	 $user = new User(); 
	 $api = new PrivateAPI();

	 /*
	 How reseting a password works:
	 1. User requests for password to be reset from loggin page by providing their email
	 2. Automated email is sent with a link that will reset their password for them. That 
	 	link will look like this: http://localhost:8888/resetpassword.php?id=1&reset_code=82ab3169d0a3774.
	 	The reset_code value is the last 15 chars of their previous password sha1. They must click this link
	 	for the password to be actually reset.
	 3. User clicks link and is automatically logged in and redirected to account.php#reset_password?temp=L3Uss8Uz. 
	 	User is then asket to change their password to something more memorable than: L3Uss8Uz
	 */

	 if(isset($_GET) &&
	 	!empty($_GET) &&
	 	isset($_GET['reset_code']) &&
	 	isset($_GET['id'])){
	 	Database::init_connection();
	 	$get_array = Database::clean($_GET);
	 	$user_obj = new stdClass();
	 	//if the user could be looked up from id
	 	if($user_obj = $api->get_logged_in_user_obj((int) $get_array['id'], true)->data[0]){
	 		//var_dump($user_obj);
	 		$old_sha1_fragment = substr($user_obj->password, 25);
	 		//echo $old_sha1_fragment;
	 		if($old_sha1_fragment == $get_array['reset_code']){
	 			$new_password = generate_password();
	 			$user->reset_password($user_obj->id, $user_obj->password, $new_password);
	 			
	 			//log the user out and restart the session
	 			Session::start();
	 			Session::destroy();
	 			Session::start();

	 			//auto sign in the user
	 			$user->sign_in($user_obj->email, $new_password);
	 			header("Location: " . Database::$root_dir_link . "/account.php?temp=" . $new_password);
	 			//reset the header to account.php/#reset_password page with a message saying that the new password is 23l3fs9slm or something
	 			//and that they should change it now.
	 		}
	 	}
	 	
	 	Database::close_connection();
	}

	//randomly generates a new password
	function generate_password($length = 8){
	    $password = "";
	    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	    $maxlength = strlen($possible);
	    if ($length > $maxlength) {
	      $length = $maxlength;
	    }
	    $i = 0; 
	    while ($i < $length) { 
	      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
	      if (!strstr($password, $char)) { 
	        $password .= $char;
	        $i++;
	      }
	    }
	    return $password;
   }
	 	

	 
	 
?> 