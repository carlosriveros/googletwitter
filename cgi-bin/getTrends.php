<?php

require_once("cgi-bin/getWOEID.php");


class GetTrends
{
	
	public function __construct(){}
	
	public function getTrends($Location)
		{

			$getWoeid = new GetWOEID;
			
			$location = $getWoeid -> getWOEID($Location);
			
			$jsonurl = "https://api.twitter.com/1/trends/$location.json";
			$json = file_get_contents($jsonurl,0,null,null);
			$json_output = json_decode($json, true);
			
			$data = $json_output[0]['trends'];
			$trendsArray = array();
			$count = 100;
			$i = 0;
			
			try{
				   foreach ($data as $trend )
				{
				   // array_push($trendsArray,array($trend['name'],$count));
				    $trendsArray[$i] = array($trend['name'],$count);
				    $count = $count - 10;
				    $i = $i+ 1;
				}
			}
			catch (Exception $e) 
			{
					$trendsArray[0] = "error";		   
						
			}

			return $trendsArray;

		}
		
}

?>