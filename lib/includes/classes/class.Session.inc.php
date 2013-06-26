<?php

class Session {

	public static function start(){
		session_start();
	}

	public static function destroy(){
		session_destroy();
		session_unset();
		$_SESSION = array(); //uncomment this if session is persisting on page
	}

	//adds assoc array values to $_SESSION superglobals on success, returns false on failure
	public static function add_session_vars($assoc_array){
		if(is_array($assoc_array)){
			foreach ($assoc_array as $key => $value) {
				$_SESSION[$key] = $value;
			}
		}
		else return false;
	}
}

?>