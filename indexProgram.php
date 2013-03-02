<?php

require_once("cgi-bin/getSession.php");
require_once("cgi-bin/main.php");
require_once("setKML.php");
require_once("class.php");
require_once("cgi-bin/getTrends.php");
$getSession = new GetSession;
$mainProgram = new Main;
$getTrends = new GetTrends;




/*Check to find 'Hashtags' or 'Trends'
 *if($_POST["rad-tweet"] =="getHashtags")
 *else if($_POST["rad-tweet"] =="getTrends")
 */
 
/*HASHTAGS*/
if($_POST["rad-tweet"] =="getHashtags"){
	$flag = 0;
	if(isset($_POST["Location"]) && isset($_POST["Since"]) && isset($_POST["Time"]) && isset($_POST["Limit"]) ){
		//current session number
		$session_id = $getSession->getSession();
		//$_SESSION['session_id'] = $session_id; 
		//execute server side code
		$tweetsToShow=$mainProgram->main($_POST["Location"],$_POST["Since"],$_POST["Time"],$_POST["Limit"],$session_id);
		
		
		$Long_Lat= $mainProgram->getLocation($_POST["Location"]);
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		$ltlngTXT = $latitude.",-,".$longitude.",-,";
		
		
		$outputTXT = $ltlngTXT."Session: ".$session_id." Results: ".$_POST["Location"]."\n,-,";
		$outputTXT = $outputTXT.date( 'Y-m-d H:i:s').",-,";
		
	
		foreach( $tweetsToShow as $tweet)
		{
		
			$Text=$tweet["Text"];
			$Count=$tweet["Count"];
			$outputTXT = $outputTXT.$Text."|";
		
		}		
	}
}	

/*TRENDS*/
else if($_POST["rad-tweet"] =="getTrends")
{
	$flag = 1;
	if(isset($_POST["Location"])){
		//current session number
		$session_id = $getSession->getSession();
		//execute server side code

		
		$tweetsToShow = $getTrends->getTrends($_POST["Location"]);
		
		$Long_Lat= $mainProgram->getLocation($_POST["Location"]);
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		$ltlngTXT = $latitude.",-,".$longitude.",-,";
		
		
		$outputTXT = $ltlngTXT."Session: ".$session_id." Results: ".$_POST["Location"]."\n,-,";
		$outputTXT = $outputTXT.date( 'Y-m-d H:i:s').",-,";
		
	
		foreach( $tweetsToShow as $trend)
		{
		
			$Text=$trend[0];
			$Count=$trend[1];
			$outputTXT = $outputTXT.$Text."|";
			
		
		}	

	}
	//FIND TRENDS HERE AND STORE AS $tweetsToShow TO CREATE GOGGLE EARTH OVERLAY
	//echo "Not implemented yet!!!\n\n";
}

//CREATE KML OVERLAY FOR GOOGLE EARTH
$time = time();
$placeMK = array();
$makeKML = new GenerateKML($time, $_POST["Location"],$longitude,$latitude,NULL,NULL,NULL,NULL);


//COLOR ALGORITHM HERE
//PRE DEFINED COLORS ff003EFF

$addLat = 0.015;
$addLong = 0.010;
$count = 0;
foreach($tweetsToShow as $tweet){
	if($_POST["rad-tweet"] =="getHashtags"){
	$Text=$tweet["Text"];
	$Count=$tweet["Count"];
	}
	else if($_POST["rad-tweet"] =="getTrends")
	{
	$Text=$tweet[0];
	$Count=$tweet[1];
	}
	if(($count % 5) ==0){
		$addLat-=0.007;
		$addLong=0.010;
				
	}
	$placeMK[] = new Placemark($time,$Text,NULL,bcadd($longitude, $addLong, 4),bcadd($latitude, $addLat, 4),$Count, $flag);
	$count++;
	$addLong-=0.010;
}

$makeKML->generatePlacemarks($placeMK);
$makeKML->export();

		$outputTXT = $outputTXT."%". $time;	
		
//respond to ajax of main
echo $outputTXT;
//echo ;
?>