<?php
require_once 'classes/new.class.API.inc.php';

$private_columns =  "id, 
					first_name, 
					last_name, 
					url, 
					email, 
					city, 
					state, 
					country, 
					zip, 
					lat, 
					lon, 
					datetime_joined, 
					description, 
					media, 
					tags, 
					organizations, 
					likes,
					password, 
					API_key, 
					API_hits, 
					verified, 
					bookmarked_users";

$api = new PrivateAPI('localhost', 'AWU', 'users', 'root', 'root');
$api->setup($public_columns);
$api->set_default_order("last_name");
$api->set_default_flow("ASC");
$api->set_searchable("first_name, last_name, email, url, description, media, tags, city, state, country");
$api->set_default_search_order("likes");
$api->set_max_output_number(1000);
$api->set_pretty_print(true);
?>