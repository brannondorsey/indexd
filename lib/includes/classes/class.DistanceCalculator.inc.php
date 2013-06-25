<?php

class DistanceCalculator{

	//returns assoc array of min lat, max lat, min lon, max lon
	//taken from http://blog.fedecarg.com/2009/02/08/geo-proximity-search-the-haversine-equation/
	public static function get_distance_range($lat, $lon, $radius_in_miles){
		$longitude = (float) $lat;//-2.708077;
		$latitude = (float) $lon;//53.754842;
		$radius = $radius_in_miles; // in miles

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

	// echo 'lng (min/max): ' . $lng_min . '/' . $lng_max . PHP_EOL;
	// echo 'lat (min/max): ' . $lat_min . '/' . $lat_max;

		/**
	 * Calculates the great-circle distance between two points, with
	 * the Haversine formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */
	protected function haversineGreatCircleDistance(
	  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);

	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;

	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
	    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}
}
?>