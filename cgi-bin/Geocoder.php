<?php

class Geocoder
{
	public function __construct(){	}
	
	public function geoCoding($Location)
	{
	
		   $a = urlencode($Location);
		   $geocodeURL = 
		   "http://maps.googleapis.com/maps/api/geocode/json?address=$a&sensor=false";
		   $ch = curl_init($geocodeURL);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   $result = curl_exec($ch);
		   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		   curl_close($ch);
		   if ($httpCode == 200) 
		   {
		      $geocode = json_decode($result);
		      $lat = $geocode->results[0]->geometry->location->lat;
		      $lng = $geocode->results[0]->geometry->location->lng; 
		      $formatted_address = $geocode->results[0]->formatted_address;
		      $geo_status = $geocode->status;
		      $location_type = $geocode->results[0]->geometry->location_type;
		   } 
		   else 
		   {
		     $geo_status = "HTTP_FAIL_$httpCode";
		   }
	
	$long_lat = array("Lat"=>$lat, "Long"=>$lng);
	return $long_lat;
	
	}
	
	public function geoCodingReverse($Lat, $Long)
	{
	
		   $c = urlencode($Lat);
		   $b = urlencode($Long);
		   $geocodeURL = 
		   "http://maps.googleapis.com/maps/api/geocode/json?latlng=$c,$b&sensor=false";
		   $ch = curl_init($geocodeURL);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   $result = curl_exec($ch);
		   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		   curl_close($ch);
 if ($httpCode == 200) 
 {
	   $geocode = json_decode($result,1);
				     
	 $parts = array
	 ( 
	 'city'=>array('locality')
	  ); 
	    if (!empty($geocode['results'][0]['address_components'])) 
	    { 
		      $ac = $geocode['results'][0]['address_components']; 
		      foreach($parts as $need=>&$types) 
		      { 
		        foreach($ac as &$a) 
		        { 
		          if (in_array($a['types'][0],$types)) $address_out[$need] = $a['short_name']; 
		          elseif (empty($address_out[$need])) $address_out[$need] = ''; 
		        } 
		      } 
	     }
		   
		      $city = $address_out['city'];
		      if($city == "")
		      {
		       if (!empty($geocode['results'][1]['address_components'])) 
	   		 { 
			      $ac = $geocode['results'][1]['address_components']; 
			      foreach($parts as $need=>&$types) 
			      { 
			        foreach($ac as &$a) 
			        { 
			          if (in_array($a['types'][0],$types)) $address_out[$need] = $a['short_name']; 
			          elseif (empty($address_out[$need])) $address_out[$need] = ''; 
			        } 
			      } 
	    		 }
	    		 $city = $address_out['city'];
		   
		      }
		      if($city == "")
		      {
				       $parts2 = array
					 ( 
					 'city'=>array('administrative_area_level_1')
					  ); 
			    if (!empty($geocode['results'][0]['address_components'])) 
			    { 
				      $ac = $geocode['results'][0]['address_components']; 
				      foreach($parts2 as $need=>&$types) 
				      { 
				        foreach($ac as &$a) 
				        { 
				          if (in_array($a['types'][0],$types)) $address_out[$need] = $a['short_name']; 
				          elseif (empty($address_out[$need])) $address_out[$need] = ''; 
				        } 
				      } 
			     }
			      $city = $address_out['city'];
		      }
		      
		      
		      /*$lng = $geocode->results[0]->geometry->location->lng; 
		      
		      
		      
		      $formatted_address = $geocode->results[0]->formatted_address;
		      $geo_status = $geocode->status;
		      $location_type = $geocode->results[0]->geometry->location_type;*/
		   } 
   else 
   {
     $geo_status = "HTTP_FAIL_$httpCode";
   }
	
	
	$location = array("location"=>$city);
	return $location;
	
}



public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') 
{ 
	$theta = $longitude1 - $longitude2; 
	$distance = (sin(deg2rad((double)$latitude1)) * sin(deg2rad((double)$latitude2))) + (cos(deg2rad((double)$latitude1)) * cos(deg2rad((double)$latitude2)) * cos(deg2rad((double)$theta))); 
	$distance = acos($distance); 
	$distance = rad2deg($distance); 
	$distance = $distance * 60 * 1.1515;
	 switch($unit) 
	 { 
		 case 'Mi': break; 
		 case 'Km' : $distance = $distance * 1.609344; 
	 } 
	 return (round($distance,2)); 
 }




}

?>