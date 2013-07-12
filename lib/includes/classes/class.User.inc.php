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
		 $b_signed_in = true; //if there is a session id
		}
		else $b_signed_in = false;
		return $b_signed_in; 
	}

	//parameters must be PRE CLEANED using Database::clean() before being passed in here
	//this function sets $_SESSION vars if login credentials pass, returns "EMAIL_NOT_CONFIRMED" if email is not confirmed,
	//and returns false if they do not.
	public function sign_in($email, $unhashed_password){
		$user_id = $this->check_sign_in_credentials($email, $unhashed_password);
		if($user_id != false){
			//echo "Logic makes since at least ";
			if($user_id == "EMAIL_NOT_CONFIRMED"){
			 return $user_id; //return "EMAIL_NOT_CONFIRMED" if email is not confirmed
			}
		}
		else return false;
		$user_data_obj = $this->get_user_data_obj($user_id);
		$user_properties = get_object_vars($user_data_obj);
		Session::add_session_vars($user_properties);
		//fill $this->data object with all of the variables from the $_SESSION
		$this->load_data(); 
		//echo " User::sign_in() is returning true ";
		return true;
	}

	//fills $this->data with all of the public user info that was saved in the user's session
	public function load_data(){
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

	//returns true on success, false on failure, and "ZIP_LOOKUP_FAILED" if zipcode lookup failed
	public function register($post_array){
		//if zip code is included look up assosciated values (i.e. city, state, country, lat, lon)
		//and append them to the end of the $post_array here:
		if(isset($post_array['zip']) &&
		   isset($post_array['country'])){ 
			if($post_array = $this->append_geo_info($post_array, $post_array['country'], $post_array['zip']));
			else return "ZIP_LOOKUP_FAILED";
		}
		else echo "there was no zip code or no country";
		$post_array = $this->add_and_encode_register_fields($post_array); //append more fields to the array
		$success = $this->IU->execute_from_assoc($post_array);
		//send email confirmation here
		return $this->send_confirmation_email($post_array['email'], $post_array['first_name'], $post_array['email_confirmation_code']);
	}

	public function delete_account(){
		//drop user row using id from $this->data obj
	}

	//returns $user_id on success, 0 if user exists but email is not confirmed, and false on falure. Used on sign in page.
	public function check_sign_in_credentials($email, $unhashed_password){
		$hashed_password = sha1($unhashed_password);
		$query = "SELECT id, email_confirmed FROM " . Database::$table . " WHERE email = '" 
		. $email . "' AND password = '" . $hashed_password . "' LIMIT 1";
		if($user = Database::get_all_results($query)){
			if($user['email_confirmed'] == 1) return $user['id'];
			else{
			 //echo "for some reason check sign in credentials things that the user's email was not confirmed";
			 return "EMAIL_NOT_CONFIRMED";
			}
		}
		else return false; //there were no matching users found
	}

	#etc...

	//change back to protected after algorithm testing
	public function get_user_data_obj($id){
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
		'email_confirmation_code' => sha1(microtime(true).mt_rand(10000,90000))
		);
		//these two fields below just check for if the likes or email_confirmation was added by the generator. 
		//these should be automatcally added to the new_fields array above before launch like they used
		//to be. 
		if(!isset($post_array['likes'])) $new_fields['likes'] = 0; 
		if(!isset($post_array['email_confirmed'])) $new_fields['email_confirmed'] = 0;
		return array_merge($post_array, $new_fields);
	}
//-------------------------------------------------------------------------------
//EMAIL CONFIRMATION

	protected function send_confirmation_email($email, $name ,$confirmation_code){
		//return true; //EDIT THIS! THIS IS SO THAT WE DON'T SPAM PEOPLE DURING DEVELOPMENT
		$path_to_email_JSON = "lib/data/email_confirmation_message.json";
		$file = file_get_contents($path_to_email_JSON);
		$email_obj = json_decode($file);
		$verification_link = Database::$root_dir_link . "email_confirmation.php?email_confirmation_code=" . $confirmation_code . "&email=" . $email;
		$email_obj->body = str_replace("REPLACE_LINK_HERE", $verification_link, $email_obj->body);
		$email_obj->body = str_replace("REPLACE_NAME_HERE", $name, $email_obj->body);

		//append and prepend html tags
		$email_obj->body = "<html><head><title>" . $email_obj->subject . "</title></head><body>" . $email_obj->body;
		$email_obj->body = $email_obj->body . "</body></html>";

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// // Additional headers
		// $headers .= 'To: Mary <mary@example.com>' . "\r\n";
		// $headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";

		return mail($email, $email_obj->subject, $email_obj->body, $headers);
	}

	//check email and conf code, if they match, set email_confirmed to 1 in the db and return true
	//if they do not match or something went wrong return false
	public function confirm_email($email, $confirmation_code){
		$query = "SELECT id, email, email_confirmation_code FROM " . Database::$table . " WHERE email = '" 
		. $email . "' LIMIT 1";
		if($user = Database::get_all_results($query)){
			//if the email and confirmation code from $_GET match the ones from the db
			if($user['email_confirmation_code'] == $confirmation_code &&
				$user['email'] == $email){
				$assoc_array = array( 'id' => $user['id'],
									  'email_confirmed' => 1);
				return $this->IU->execute_from_assoc($assoc_array, "UPDATE", "email_confirmed"); //update the user's email_confirmed to 1
			} 
		} else echo "Error: no results found for this user";
		return false;
	}

	//returns true if email already exists and false if it does not. 
	public function email_already_exists($email){
		$query = "SELECT email FROM " . Database::$table . " WHERE email = '" . $email . "' LIMIT 1";
		return ($user = Database::get_all_results($query) ? true: false); 
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
		else return false; //geo json object failed to load
		return array_merge($array_to_append, $geo_info_array);
	}

	//returns returns zippopotamus json obj 
	protected function get_geo_info_obj($country, $zip){
		$api_url = "http://api.zippopotam.us/" . $country . "/" . $zip;
		$json = file_get_contents($api_url);
		if($json === FALSE) return false;
		$json = str_replace("place name", "place_name", $json); //replace space in place name
		$geo_data_obj = json_decode($json);
		if($geo_data_obj != NULL) return $geo_data_obj;
		else return false; 
	}
}
?>