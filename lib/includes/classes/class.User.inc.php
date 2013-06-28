<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.Session.inc.php");
require_once("class.InsertUpdate.inc.php");

class User{
	protected $api;
	protected $id;
	public $data; //holds all of the users info (PUBLIC ONLY)
	protected $b_signed_in = false;
	protected $IU; //InsertUpdate class

	public function __construct(){
		$this->api = new PrivateAPI();
		$this->IU = new InsertUpdate();
	}
	//checks if user is signed in by their PHPSESSID cookie and returns what it finds
	public function is_signed_in(){
		if(isset($_SESSION['id'])){
		 $this->fill_data_var_from_SESSION(); //load the data object
		 $b_signed_in = true; //if there is a session id
		}
		else $b_signed_in = false;
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
		$this->fill_data_var_from_SESSION();
	}

	//fills $this->data with all of the public user info that was saved in the user's session
	public function fill_data_var_from_SESSION(){
		$this->data = new stdClass();
		foreach($_SESSION as $key => $value){
			$this->data->{$key} = $value;
		}
	}

	public function sign_out(){
		Session::destroy(); //destroy the logged in user session
	}

	public function update_profile(){

	}

	public function register($post_array){
		//if zip code is included look up assosciated values (i.e. city, state, country, lat, lon)
		//and append them to the end of the $post_array here:
		if(isset($post_array['zip']) &&
		   isset($post_array['country'])) $post_array = $this->append_geo_info($post_array, $post_array['country'], $post_array['zip']);
		else echo "there was no zip code or no country";
		$post_array = $this->add_and_encode_register_fields($post_array); //append more fields to the array
		$this->IU->execute_from_assoc($post_array);
	}

	public function delete_account(){
		//drop user row using id from $this->data obj
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

	//encodes passwords and assigns fields like api key, verified, etc... 
	//also cleans the post array
	protected function add_and_encode_register_fields($post_array){
		$date_time = new DateTime();
		$timestamp = $date_time->format(DateTime::ISO8601);
		$post_array['password'] = sha1($post_array['password']); //encrypt password
		$new_fields = array(
		'datetime_joined' => $timestamp, //save joined to now
		'API_key' => sha1(microtime(true).mt_rand(10000,90000)), //generate random key
		'verified' => 0,
		'likes' => 0,
		'email_confirmation_code' => sha1(microtime(true).mt_rand(10000,90000)),
		'email_confirmed' => 0);
		return array_merge($post_array, $new_fields);
	}

//-------------------------------------------------------------------------------
//GEO FUNCTIONS

	protected function append_geo_info($array_to_append, $country, $zip){
		$geo_info_array = array();
		if($geo_obj = $this->get_geo_info_obj($country, $zip)){
			//$object_vars = get_object_vars($geo_obj);
			//var_dump($geo_obj);
			//echo "is object " . is_object($geo_obj->places);
			 $geo_info_array['lat'] = $geo_obj->places[0]->latitude;
			 $geo_info_array['lon'] = $geo_obj->places[0]->longitude;
			 $geo_info_array['city'] = $geo_obj->places[0]->place_name;
			if(isset($geo_obj->places[0]->state)) $geo_info_array['state'] = $geo_obj->places[0]->state;
		}
		else echo "geo json object failed to load"; //return false <---- later
		return array_merge($array_to_append, $geo_info_array);
	}

	//returns returns zippopotamus json obj 
	protected function get_geo_info_obj($country, $zip){
		$api_url = "http://api.zippopotam.us/" . $country . "/" . $zip;
		$json = file_get_contents($api_url);
		$json = str_replace("place name", "place_name", $json); //replace space in place name
		$geo_data_obj = json_decode($json);
		if($geo_data_obj != NULL) return $geo_data_obj;
		else return false; 
	}
}
?>