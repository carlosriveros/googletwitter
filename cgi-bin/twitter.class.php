<?php

class Twitter
{
	
	public function __construct(){	}
	
	public function searchResultsLatLong($Lat, $Long)
	{	
		$url = "http://search.twitter.com/search.atom?q=&geocode=$Lat,$Long,5km&rpp=100";
		
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $curl );
		curl_close( $curl );
		
		$return = new SimpleXMLElement( $result );
		return $return;
		
		
	}
	
	
}

?>