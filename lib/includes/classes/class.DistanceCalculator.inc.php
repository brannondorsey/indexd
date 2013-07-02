<?php

class DistanceCalculator{

    public static function get_distance_mysql_query($user_id, $lat, $lon, $radius_in_miles, $limit, $columns){
		$location_range = self::get_distance_range($lat, $lon, $radius_in_miles);
		$query = "SELECT " . $columns . ", ( 3959 * acos( cos( radians(
		$lat) ) * cos( radians( 
		users.lat ) ) * cos( radians( 
		users.lon ) - radians(
		$lon) ) + sin( radians(
		$lat) ) * sin( radians( 
		users.lat ) ) ) ) AS distance 
		FROM users WHERE lat >=" . $location_range['min lat'] . " AND lat <=" . $location_range['max lat']
		. " AND lon >= " . $location_range['min lon'] . " AND lon <= " . $location_range['max lon'] . " AND id != '$user_id'
		ORDER BY distance LIMIT $limit";
		return $query;
	}

	//returns assoc array of min lat, max lat, min lon, max lon
	//taken from http://blog.fedecarg.com/2009/02/08/geo-proximity-search-the-haversine-equation/
	protected static function get_distance_range($lat, $lon, $radius_in_miles){
		$longitude = $lon;//-2.708077;
		$latitude = $lat;//53.754842;
		$radius = $radius_in_miles; // in miles
		// echo "the latitude is " . $latitude . "<br/>";
		// echo "the longitude is " . $longitude . "<br/>";
		// echo "the radius in miles is " . $radius . "<br/>";

		$lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
		$lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
		$lat_min = $latitude - ($radius / 69);
		$lat_max = $latitude + ($radius / 69);
		return array(
				"min lat" => $lat_min,
				"max lat" => $lat_max,
				"min lon" => $lng_min,
				"max lon" => $lng_max,
		);
	}
}
?>