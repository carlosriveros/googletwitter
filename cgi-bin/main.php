<?php
require_once("cgi-bin/Geocoder.php");
require_once("cgi-bin/createOutput.php");
require_once("cgi-bin/twitter.class.php");
require_once("cgi-bin/loadIntoData.php");


class Main{
	
	public function __construct(){}
	
	public function getLocation($Location){
		
		$Geocoder2 = new Geocoder;
		$Long_Lat = $Geocoder2->geoCoding($Location);
		
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		
		return $Long_Lat;
	}
 	public function main($Location,$unitNum,$timeUnit,$numResults,$session) {
		
		$Loader = new LoadIntoData;
		$Geocoder = new Geocoder;
		$Twitter = new Twitter;
		$Output = new CreateOutput;
		
		//$Location = 'Toronto';
	

		$con = mysql_connect(localhost,"slidat_carlos","cortes299940");
	        mysql_select_db("slidat_cps630") or die( "Unable to select database");
		
		
		
		$Long_Lat = $Geocoder->geoCoding($Location);
		
		$latitude = $Long_Lat["Lat"];
		$longitude = $Long_Lat["Long"];
		//echo "main:".$longitude;
		$Loc = $Geocoder->geoCodingReverse($latitude,$longitude);
		$new_location = $Loc["location"];
		
		
		$radius = 10;
		
		$qry = "SELECT Location, Latitude, Longitude FROM Locations";
	        //$qry = "SELECT * from Locations";
	        $resource =  mysql_query($qry);
	        
	        $location_table_size = 0;
	        
	         if(mysql_num_rows(($resource)) > 0)
	   	 {
	   	  
		   	  $location_table_size = 1;
		   	
	   	 }
	        
	        while ($row = mysql_fetch_array($resource, MYSQL_ASSOC))
		 {
		      
   			$row_location = $row["Location"];
   			$row_lat = $row["Latitude"];
   			$row_long = $row["Longitude"];
   			$distance = $Geocoder->getDistanceBetweenPointsNew($latitude,$longitude,$row_lat,$row_long);
			
			
			//echo "distance= ". $distance ."  ";
			
			
			if($distance < $radius)
			{
			
			$matched = 1;
			break;
			
			
			}
			elseif($distance >= $radius)
			{
			 $matched = 0;
			  
			}
			
			
		}
		
		if($matched == 1 && $latitude != '' && $longitude != '' )
		{
		
		  
			  
			$results = $Twitter->searchResultsLatLong($latitude,$longitude);
			
			
			$Loader->loadIntoData($results, $new_location, $latitude, $longitude);
			
			
			
			$querys2 = "SELECT Text FROM data WHERE Location = '$new_location'";
			$response2 = mysql_query($querys2);
			
			 $myDate = date( 'y-m-d H:i:s');
		        mysql_query("UPDATE Locations SET Date = '$myDate' WHERE Location = '$new_location'");
			
			/*
			echo "<h3><u>DATA</u></h3>";
			$counter = 1;
			while (($row2 = mysql_fetch_array($response2, MYSQL_ASSOC)))
			 {
				 if($counter > 20)
				 {
				   break;
				 }
			 echo"<h3>Text-hashtag:   ". $row2["Text"] ."</h3>";
			 $counter = $counter + 1;
			 }
			*/
				
		
		}
		elseif(($matched == 0 || $location_table_size == 0) && $latitude != '' && $longitude != '')
		{
		//new location
		$myDate = date( 'y-m-d H:i:s');
		
		$query = "INSERT INTO Locations Values ('$new_location', '$latitude', '$longitude','$myDate')";
			  			  $response =  mysql_query($query);
			//echo "balls";
			
			$results = $Twitter->searchResultsLatLong($latitude,$longitude);
		//$command = "/usr/local/bin/php -f /home/slidat/public_html/cgi-bin/loadIntoDataCL.php -a $results -b $new_location -c $latitude -d $longitude";
		//	exec( "$command > /dev/null &");
			$Loader->loadIntoData($results, $new_location, $latitude, $longitude);
			
		}
		mysql_close(); 
		//echo "Create output";
		//Create output table for client. Based on client fiends
		return $Output->createOutput($new_location,$unitNum,$timeUnit,$numResults,$session);

	
	}
}
?>