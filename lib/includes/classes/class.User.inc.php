<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("classes/class.Session.inc.php");

class User{
	protected $api;
	protected $id;
	public $data; //holds all of the users info
	protected $b_signed_in = false;

	public function __construct(){
		$this->api = new PrivateAPI();
	}
	//checks if user is signed in using $_SESSION and returns what it finds
	public function is_signed_in(){
		if(isset($_SESSION['id'])) $b_signed_in = true; //if there is a session id
		else $b_signed_in = false;
		//return true; //for now
		return $b_signed_in; 
	}

	//parameters must be PRE CLEANED using Database::clean() before being passed in here
	//this function sets $_SESSION vars if login credentials pass and returns false if they do not.
	//Loads $this->data object with user vars on successful login
	public function sign_in($email, $unhashed_password){
		if($user_id = $this->check_sign_in_credentials($email, $unhashed_password));
		else{ 
			echo "user authentication failed";
			return false;
		}
		$user_data_obj = $this->get_user_data_obj($user_id);
		$user_properties = get_object_vars($user_data_obj);
		Session::add_session_vars($user_properties);
		//fill $this->data object with all of the variables from the $_SESSION
		$this->data = new stdClass();
		foreach($_SESSION as $key => $value){
			$this->data->{$key} = $value;
			//echo "the value I just set was " . $this->data->{$key} . "<br/>";
		}
		//echo $this->data->first_name;
	}

	public function sign_out(){
		Session::destroy(); //destroy the logged in user session
	}

	public function update_profile(){

	}

	public function register(){

	}

	public function delete_account(){

	}

	//returns $user_id on success and false on falure. Used on sign in page.
	public function check_sign_in_credentials($email, $unhashed_password){
		$hashed_password = sha1($unhashed_password);
		$query = "SELECT id FROM " . Database::$table . " WHERE email = '" 
		. $email . "' AND password = '" . $hashed_password . "' LIMIT 1";
		if($user = Database::get_all_results($query)) return $user['id'];
		else return false; //there were no matching users found
	}

	#etc...

	protected function get_user_data_obj($id){
		if($obj = $this->api->get_logged_in_user_obj($id));
		else echo "PROBLEM DECODING JSON OBJECT ";
		return $obj->data[0]; //asign all of the JSON obj key value pairs to the user's $data object
	}
}
?>