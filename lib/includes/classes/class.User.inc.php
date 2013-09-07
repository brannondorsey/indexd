<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.Session.inc.php");
require_once("class.InsertUpdate.inc.php");
require_once("class.BookmarkHandler.inc.php");

class User{
	protected $api;
	protected $id;
	public $data; //holds all of the users info (PUBLIC ONLY)
	protected $b_signed_in = false;
	protected $IU; //InsertUpdate class
	protected $bookmark_hand;

	public function __construct(){
	 	$api = new PrivateAPI("localhost", "AWU", "users", "root", "root");
		$this->IU = new InsertUpdate();
		$this->bookmark_hand = new BookmarkHandler();
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

	//profile must be pre cleaned
	public function update_profile($post_array){
		$changed = array();

		//fill the array with each change
    	foreach($post_array as $key => $value){
    		if($key == 'email' && str_replace('http://', '', $this->data->{$key}) == $value) continue;
        	else if($value != $this->data->{$key}) $changed[$key] = $value;
    	}
    	//if there was a change made
    	if(isset($changed) && !empty($changed)){
    		$changed['id'] = $post_array['id'];
    		$this->IU->execute_from_assoc($changed, "UPDATE");
    	}
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

	//NOTE: the $old_password param may be hashed OR unhashed
	//resets the user's password in the database. Returns false if something went wrong
	public function reset_password($user_id, $old_password, $new_password_unhashed){
		//if the user id is correct and a user was found
		if($user_obj = $this->api->get_logged_in_user_obj((int) $user_id, true)->data[0]){
			//if the old password is correct
			//checks for hashed password OR unhashed
			if($old_password == $user_obj->password ||
				Database::hasher($old_password, $user_obj->password)){
				$assoc_array = array('id' => $user_id, 
									'password' => Database::hasher(	$new_password_unhashed));
				return $this->IU->execute_from_assoc($assoc_array, 'UPDATE', 'password');
			}else return false;
		}else return false;
	}

	//should type password before deleting account
	public function delete_account(){
		//drop user row using id from $this->data obj
	}

	//bookmarks a user by bookmarked user's id
	//returns false on failure
	public function add_bookmark($id_of_bookmarked_users){
		return $this->bookmark_hand->add_bookmark($this->data->id, $id_of_bookmarked_users);
	}

	//returns $user_id on success, "EMAIL_NOT_CONFIRMED" if user exists but email is not confirmed, and false on falure. Used on sign in page.
	public function check_sign_in_credentials($email, $unhashed_password){
		//$hashed_password = Database::hasher($unhashed_password);
		$query = "SELECT id, password, email_confirmed FROM " . Database::$table . " WHERE email = '" 
		. $email . "' LIMIT 1";
		if($user = Database::get_all_results($query)){
			$user = $user[0];
			//if unhashed password matches the password from the database lookup
			if(Database::hasher($unhashed_password, $user['password'])){
				if($user['email_confirmed'] == 1) return $user['id'];
				else{
				 //echo "for some reason check sign in credentials things that the user's email was not confirmed";
				 return "EMAIL_NOT_CONFIRMED";
				}
			}else return false; //there were no matching users found
		}
		else return false; //there were no matching users found
	}

	#etc...

	//change back to protected after algorithm testing
	public function get_user_data_obj($id){
		if($obj = $this->api->get_logged_in_user_obj($id, true));
		else echo "PROBLEM DECODING JSON OBJECT ";
		return $obj->data[0]; //asign all of the JSON obj key value pairs to the user's $data object
	}

	//encodes passwords and assigns fields like api key, verified, etc... 
	//also cleans the post array
	protected function add_and_encode_register_fields($post_array){
		$date_time = new DateTime();
		$timestamp = $date_time->format(DateTime::ISO8601);
		$post_array['password'] = Database::hasher($post_array['password']); //encrypt password
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

	//sends an email with a link to reset a user's password.
	//returns true on success and false on failure
	public function send_reset_password_email($email){
		if($this->email_already_exists($email)){
			$query_array = array('email' => $email,
								 'exact' => 'true',
								 'limit' => '1');
			$user_obj = json_decode($this->api->get_json_from_assoc($query_array))->data[0];
			$password_reset_link = Database::$root_dir_link . "resetpassword.php?id=" . $user_obj->id . "&reset_code=" . substr($user_obj->password, 67);
			$path_to_email_JSON = Database::$root_dir_link . "lib/data/password_reset_message.json";
			$file = file_get_contents($path_to_email_JSON);
			$email_obj = json_decode($file);
			$email_obj->body = str_replace("REPLACE_LINK_HERE", $password_reset_link, $email_obj->body);
			$email_obj->body = str_replace("REPLACE_NAME_HERE", $user_obj->first_name, $email_obj->body);

			//append and prepend html tags
			$email_obj->body = "<html><head><title>" . $email_obj->subject . "</title></head><body>" . $email_obj->body;
			$email_obj->body = $email_obj->body . "</body></html>";

			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			return mail($user_obj->email, $email_obj->subject, $email_obj->body, $headers);
		}else return false;

	}

	//check email and conf code, if they match, set email_confirmed to 1 in the db and return true
	//if they do not match or something went wrong return false
	public function confirm_email($email, $confirmation_code){
		$query = "SELECT id, email, email_confirmation_code FROM " . Database::$table . " WHERE email = '" 
		. $email . "' LIMIT 1";
		if($user = Database::get_all_results($query)){
			$user = $user[0];
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
		$user = Database::get_all_results($query);
		return ($user[0]) ? true: false; 
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