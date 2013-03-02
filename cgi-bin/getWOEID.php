<?php
require_once("cgi-bin/Geocoder.php");

class GetWOEID
{
		

	public function __construct(){	}
	
	public function getWOEID($location)
	{
	$GeocoderR = new Geocoder;
	$Long_Lat = $GeocoderR->geoCoding($location);
		
	$latitude = $Long_Lat["Lat"];
	$longitude = $Long_Lat["Long"];
	
	

	

 
	$url_post = "http://where.yahooapis.com/v1/places.q('".urlencode($location)."')?appid=26vtqA3IkY33IePAzOJso2VF9Ywrisdq4XzshIm0v3jNw1MfzIFhu7M9dGzxQtXz";
	$weather_feed = file_get_contents($url_post);
	$objDOM = new DOMDocument();
	$objDOM->loadXML($weather_feed);
	$woeid = $objDOM->getElementsByTagName("place")->item(0)->getElementsByTagName("woeid")->item(0)->nodeValue;
 
	return $woeid;
		
	
	}
	
	
}

?>