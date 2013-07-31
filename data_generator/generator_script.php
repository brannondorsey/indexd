<?php 
    require_once '../lib/includes/classes/class.User.inc.php';
    require_once '../lib/includes/classes/class.Validator.inc.php';

	$user = new User();
	$validator = new Validation();

	Database::init_connection();

	//number of users to add to the db
	$numb_users_to_generate = 2000;

	//settings
	$max_description_chars = 140;
	$max_media = 4;
	$max_tags = 8;
	$max_organizations = 2;
	$max_likes = 1400;

	//load files
	$first_names_male_array = file("data/first_names_male.txt");
	$first_names_female_array = file("data/first_names_female.txt");
	$last_names_array = file("data/last_names.txt");
	$lorem_string = file_get_contents("data/lorem.txt");
	$media_array = file("data/media.txt");
	$tags_array = file("data/tags.txt");
	$organizations_array = file("data/organizations.txt");
	$zip_array = explode("\r", file_get_contents("data/zip_codes.txt"));

	//generate each user in here
	for($i = 0; $i < $numb_users_to_generate; $i++){
		
		//pick names
		$first_names_array = (floor(rand(0, 2) == 1) ? $first_names_male_array : $first_names_female_array); //random male or female name
		$first_name_index_max = sizeof($first_names_array);
		$first_name_index = floor(rand(0, $first_name_index_max -1)); //pick the index of the first name 
		$first_name = trim($first_names_array[$first_name_index]); //assign first name
		$last_name_index_max = sizeof($last_names_array);
		$last_name_index = floor(rand(0, $last_name_index_max -1));
		$last_name = trim($last_names_array[$last_name_index]);

		//pick email
		$email = $first_name . $last_name . "@mailinator.com";

		//pick url
		$url_prefixes = array("http://", "www.", "http://www.", "");  
		$url_prefix_index = floor(rand(0, sizeof($url_prefixes)-1));
		$url = $url_prefixes[$url_prefix_index]; //select url prefix
		$url .= $first_name . $last_name . ".com"; //set email to names combined
		
		//password is set to user's first name concatonated with last name
		$password = $first_name . $last_name;

		//description
		$description_length = floor(rand(71, $max_description_chars + 1)); //pick random length between 70 and 140
		$description_start = floor(rand(0, strlen($lorem_string)-$description_length)); //pick a random start point
		$description = substr($lorem_string, $description_start, $description_length); //select a substring for desc.

		//media
		$media = get_list($max_media, $media_array);

		//tags
		$tags = get_list($max_tags, $tags_array);

		//organizations
		$organizations = get_list($max_organizations, $organizations_array);

		//zip
		$zip = get_list(1, $zip_array);

		//likes
		$likes = floor(rand(0, $max_likes));

		$new_user_array = array( 'first_name' => ucfirst($first_name),
								 'last_name' => ucfirst($last_name),
								 'email' => $email,
								 'url' => $url,
								 'password' => $url,
								 'description' => $description,
								 'media' => $media,
								 'tags' => $tags,
								 'organizations' => $organizations,
								 'likes' => $likes,
								 'zip' => $zip,
								 'country' => "us");
		$new_user_array = Database::clean($new_user_array);
		$new_user_array['url'] = $validator->processURLString($new_user_array['url']);
		if($user->register($new_user_array) !== "ZIP_LOOKUP_FAILED"){
			echo "Added " . ucfirst($first_name) . " " . ucfirst($last_name) . " to the database <br/>";
		}else echo "Zip lookup failed I did not add " . ucfirst($first_name) . " " . ucfirst($last_name) . " to the database <br/>";
	}
	Database::close_connection();


//----------------------------------------------------------------------------
//HELPERS

	//used for media and tags. Returns a comma-space dilemited list string.
	//$max is the max number of list items to return
	//$array is the array of list items to chose from (an array from a loaded .txt file)
	function get_list($max, $array){
		//media
		$numb_list_items = ($max > 1 ? floor(rand(1, $max + 1)) : 1);
		$list_items_string = "";
		$repeat_check = array();
		for($j = 0; $j <$numb_list_items; $j++){
			$index = floor(rand(0, sizeof($array)-1));
			$selection = trim($array[$index]);
			//if the selection wasnt already selected
			if(!in_array($selection, $repeat_check)){
				$list_items_string .= ", " . $selection; //add it to the string to return
				$repeat_check[] = $selection; //add it to repeat check
			}
		}
		$list_items_string = trim($list_items_string, ", "); //chop off the first comma-space
		return $list_items_string;
	}

?>