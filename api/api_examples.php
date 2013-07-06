<?php 
	$city = "Baltimore";
	$state = "Maryland";
	$media = "Sculpture";

	$http_request = "http://localhost:8888/api/api.php?city=". $city
	 . "&state=" . $state . "&media=" . $media;
	
	$json_string = file_get_contents($http_request);
	$jsonObj = json_decode($json_string);
	
	//loop through each user object inside of the "data" array
	foreach($jsonObj->data as $user){
		//do something with each result inside of here...
		//for example, print some of their info to the browser
		echo "This user's first name is " . $user->first_name . "<br/>";
		echo "This user's last name is " . $user->last_name . "<br/>";
		echo "This user's website is " . $user->url . "<br/>";
		echo "<br/>";
	}
?>