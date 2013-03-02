<?php
require_once("cgi-bin/Geocoder.php");
require_once("cgi-bin/twitter.class.php");
require_once("cgi-bin/loadIntoData.php");
$con = mysql_connect(localhost,"slidat_carlos","cortes299940");
mysql_select_db("slidat_cps630") or die( "Unable to select database");
$Loader = new LoadIntoData;
		$Geocoder = new Geocoder;
		$Twitter = new Twitter;
if($_GET["RadTweet"] =="getHashtags")
{
  	
	$Tweet = $_GET["Tweet"];
	$BeginTime= $_GET["BeginTime"];
	$Location = $_GET["Location"];
	$Since= $_GET["Since"];
	$TimeUnit = $_GET["TimeUnit"];
	
	$Long_Lat = $Geocoder->geoCoding($Location);
		
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		//echo "main:".$longitude;
		$Loc = $Geocoder->geoCodingReverse($latitude,$longitude);
		$new_location = $Loc["location"];
		        
	$sqlQuery = "SELECT Text, Username FROM data WHERE Hashtags LIKE '%$Tweet%' AND Location = '$new_location' AND DATE > DATE_SUB( '$BeginTime' , INTERVAL $Since $TimeUnit)";
	$R= mysql_query($sqlQuery) or die($BeginTime." ".mysql_error());
	
	$TweetsWithHashtag = "";
	while ($row = mysql_fetch_array($R,MYSQL_ASSOC))
	{
	        $array = explode(' ',$row["Username"]);
	
		//$TweetsWithHashtag = $TweetsWithHashtag.$row["Text"]."<br/><br/>";
		$TweetsWithHashtag = $TweetsWithHashtag."<div id=\"atweet\"><div id=\"tweettext\">".$row["Text"]."</div><div id=\"usertweet\"><span id=\"by\">By:&nbsp;</span>".$array[0]."</div></div>";
	}
   
}
else if($_GET["RadTweet"] =="getTrends")
{        $BeginTime= $_GET["BeginTime"];
	$Tweet = $_GET["Tweet"];
	$Location = $_GET["Location"];
	
	$Long_Lat = $Geocoder->geoCoding($Location);
		
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		//echo "main:".$longitude;
		$Loc = $Geocoder->geoCodingReverse($latitude,$longitude);
		$new_location = $Loc["location"];
		
		$results = $Twitter->searchResultsLatLong($latitude,$longitude);
		$Loader->loadIntoData($results, $new_location, $latitude, $longitude);
		$Loader->loadIntoData($results, $new_location, $latitude, $longitude);
	$sqlQuery2 = "SELECT Text, Username FROM data WHERE Text LIKE '%$Tweet%' AND Location = '$new_location' AND DATE > DATE_SUB( NOW() , INTERVAL 4 HOUR)";
	$R= mysql_query($sqlQuery2) or die(" ".mysql_error());
	
	$TweetsWithHashtag = "";
	while ($row = mysql_fetch_array($R,MYSQL_ASSOC))
	{
	        $array = explode(' ',$row["Username"]);
	
		//$TweetsWithHashtag = $TweetsWithHashtag.$row["Text"]."<br/><br/>";
		$TweetsWithHashtag = $TweetsWithHashtag."<div id=\"atweet\"><div id=\"tweettext\">".$row["Text"]."</div><div id=\"usertweet\"><span id=\"by\">By:&nbsp;</span>".$array[0]."</div></div>";
	}
	
	
	

}
echo $TweetsWithHashtag;
mysql_close(); 



?>