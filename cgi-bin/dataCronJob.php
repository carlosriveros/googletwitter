<?php

require_once("twitter.class.php");
require_once("loadIntoData.php");
$Loader = new LoadIntoData;
$Twitter = new Twitter;

$con = mysql_connect(localhost,"slidat_carlos","cortes299940");
mysql_select_db("slidat_cps630") or die( "Unable to select database");
		
		
$query = "SELECT Location, Latitude, Longitude FROM Locations ORDER BY Date ASC LIMIT 0,5";

$resource =  mysql_query($query);

while ($row = mysql_fetch_array($resource, MYSQL_ASSOC))
 {

	 
	  $location = $row["Location"];
	  $lat = $row["Latitude"];
	  $long = $row["Longitude"];
	  $myDate = date( 'y-m-d H:i:s');
	  
	  
	  $results = $Twitter->searchResultsLatLong($lat,$long);
	  $Loader->loadIntoData($results, $location, $lat, $long);
	  
	
	  mysql_query("UPDATE Locations SET Date = '$myDate' WHERE Location = '$location'");
	 
 }
 
 mysql_close(); 

?>
