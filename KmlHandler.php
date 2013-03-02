<?php

require 'KmlEngine.php';

$latitude = $_POST["lat"];
$longitude = $_POST["lng"];
$woeid = $_POST["woeid"];

getTrends($woeid, $latitude, $longitude);


/*function makeMarker($name,$index,$latitude,$longitude,$height){
	$tempMarker = new Marker();
	
	$tempMarker->latitude = $latitude;
	$tempMarker->longitude = $longitude;
	$tempMarker->latitude = $latitude;
	$tempMarker->altitude = $height;
	$tempMarker->color = 'F93F93';
	$tempMarker->name = 'F93F93';
	
	
	$marker5->trendNum = $index;
	$marker5->trendName = $name;
	$marker5->trendUrl = $trendUrlArr[4];

}*/

//get trends from twitter api
function getTrends($name, $latitude, $logitude,$size){
	ds($woeId, $latitude, $longitude) {
    $url = "http://api.twitter.com/1/trends/" . $woeId . ".json";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);

    $obj = json_decode($result, true);
    $trendNameArr = array();
    $trendUrlArr = array();

    /* $this->responseInfo=curl_getinfo($curl);

      if( intval( $this->responseInfo['http_code'] ) == 200 )
      return $response;
      else
      return false; */

    //print_r($obj[0]);
    $objArray = $obj[0];
    $trendsArray = $objArray[trends];
    $locationArray = $objArray[locations];
    $tArray = $obj[0]->trends;//returns null
   
   
    //$trends = array_values(array_values($trends["trends"]));
    //print_r($trends[0]);
    foreach ($trendsArray as $trend) {
        //echo $trend["name"] . " ";
        $tName = $trend["name"];
        array_push($trendNameArr,$tName);
        $tUrl = $trend["url"];
        array_push($trendUrlArr,$tUrl);
    }
    curl_close($curl);

    //dataStore is array of ten marker objects
    $dataStoreArray = array();

    $marker1 = new Marker();
    $marker1->latitude = $latitude;
    $marker1->longitude = $longitude;
    $marker1->altitude = 50;
    $marker1->color = '99FFFF';
    $marker1->trendNum = 1;    
    $marker1->trendName = $trendNameArr[0];
    $marker1->trendUrl = $trendUrlArr[0];
    array_push($dataStoreArray, $marker1);

    $distance = 2;  // distance    

    $marker2 = new Marker();
    //top_right 
    $marker2Coord = bpot_getDueCoords($latitude, $longitude, 45, $distance, km, true);
    $marker2->latitude = $marker2Coord['lat'];
    $marker2->longitude = $marker2Coord['lng'];
    $marker2->altitude = 45;
    $marker2->color = '120D16';
    $marker2->trendNum = 2;
    $marker2->trendName = $trendNameArr[1];
    $marker2->trendUrl = $trendUrlArr[1];
    array_push($dataStoreArray, $marker2);

    $marker3 = new Marker();
    //bottom_right
    $marker3Coord = bpot_getDueCoords($latitude, $longitude, 135, $distance, km, true);
    $marker3->latitude = $marker3Coord['lat'];
    $marker3->longitude = $marker3Coord['lng'];
    $marker3->altitude = 40;
    $marker3->color = 'C997FC';
    $marker3->trendNum = 3;
    $marker3->trendName = $trendNameArr[2];
    $marker3->trendUrl = $trendUrlArr[2];
    array_push($dataStoreArray, $marker3);
    
    $marker4 = new Marker();
    //bottom_left 
    $marker4Coord = bpot_getDueCoords($latitude, $longitude, 225, $distance, km, true);
    $marker4->latitude = $marker4Coord['lat'];
    $marker4->longitude = $marker4Coord['lng'];
    $marker4->altitude = 35;
    $marker4->color = 'F3CF3C';
    $marker4->trendNum = 4;
    $marker4->trendName = $trendNameArr[3];
    $marker4->trendUrl = $trendUrlArr[3];
    array_push($dataStoreArray, $marker4);
    
    $marker5 = new Marker();
    //top_left
    $marker5Coord = bpot_getDueCoords($latitude, $longitude, 315, $distance, km, true);
    $marker5->latitude = $marker5Coord['lat'];
    $marker5->longitude = $marker5Coord['lng'];
    $marker5->altitude = 30;
    $marker5->color = 'F93F93';
    $marker5->trendNum = 5;
    $marker5->trendName = $trendNameArr[4];
    $marker5->trendUrl = $trendUrlArr[4];
    array_push($dataStoreArray, $marker5);
    
    $marker6 = new Marker();
    //top_left with double distance
    $marker6Coord = bpot_getDueCoords($marker2->latitude, $marker2->longitude, 315, $distance*2, km, true);
    $marker6->latitude = $marker6Coord['lat'];
    $marker6->longitude = $marker6Coord['lng'];
    $marker6->altitude = 25;
    $marker6->color = '03F03F';
    $marker6->trendNum = 6;
    $marker6->trendName = $trendNameArr[5];
    $marker6->trendUrl = $trendUrlArr[5];
    array_push($dataStoreArray, $marker6);
    
    
    $marker7 = new Marker();
    //bottom_right
    $marker7Coord = bpot_getDueCoords($marker2->latitude, $marker2->longitude, 135, $distance, km, true);
    $marker7->latitude = $marker7Coord['lat'];
    $marker7->longitude = $marker7Coord['lng'];
    $marker7->altitude = 20;
    $marker7->color = 'FF0FF0';
    $marker7->trendNum = 7;
    $marker7->trendName = $trendNameArr[6];
    $marker7->trendUrl = $trendUrlArr[6];
    array_push($dataStoreArray, $marker7);

    
    $marker8 = new Marker();
    //bottom_left
    $marker8Coord = bpot_getDueCoords($marker5->latitude, $marker5->longitude, 225, $distance, km, true);
    $marker8->latitude = $marker8Coord['lat'];
    $marker8->longitude = $marker8Coord['lng'];
    $marker8->altitude = 15;
    $marker8->color = '0F00F0';
    $marker8->trendNum = 8;
    $marker8->trendName = $trendNameArr[7];
    $marker8->trendUrl = $trendUrlArr[7];
    array_push($dataStoreArray, $marker8);
    
    $marker9 = new Marker();
    //bottom_left * 2
    $marker9Coord = bpot_getDueCoords($marker5->latitude, $marker5->longitude, 225, 2*$distance, km, true);
    $marker9->latitude = $marker9Coord['lat'];
    $marker9->longitude = $marker9Coord['lng'];
    $marker9->altitude = 10;
    $marker9->color = 'C0CC0C';
    $marker9->trendNum = 9;
    $marker9->trendName = $trendNameArr[8];
    $marker9->trendUrl = $trendUrlArr[8];
    array_push($dataStoreArray, $marker9);
    
    $marker10 = new Marker();
    //bottom_right * 2
    $marker10Coord = bpot_getDueCoords($marker2->latitude, $marker2->longitude, 135, 2*$distance, km, true);
    $marker10->latitude = $marker10Coord['lat'];
    $marker10->longitude = $marker10Coord['lng'];
    $marker10->altitude = 5;
    $marker10->color = 'F60F60';
    $marker10->trendNum = 10;
    $marker10->trendName = $trendNameArr[9];
    $marker10->trendUrl = $trendUrlArr[9];
    array_push($dataStoreArray, $marker10);


// Create thematic map object
    $map = new KmlMaker($dataStoreArray);

// Create KML
  $file = $map->getKML();  

  // Return JSON with file url
  $result = array('success'=>'true','file'=>$file);
   echo json_encode($result);
    // echo "{success: true, file: '$file'}";
}

//create marker object
class Marker {

    //trend info obtained from twitter api
    public $trendNum;
    public $trendName;
    public $trendUrl;
    //location info for first marker obtained from yahoo api
    public $longitude;
    public $latitude;
    //set manually
    public $color;
    public $altitude;

}

// Modified from:
// http://www.sitepoint.com/forums/showthread.php?656315-adding-distance-gps-coordinates-get-bounding-box
/**
 * bearing is 0 = north, 180 = south, 90 = east, 270 = west
 *
 */
function bpot_getDueCoords($latitude, $longitude, $bearing, $distance, $distance_unit = "km", $return_as_array = FALSE) {

    if ($distance_unit == "m") {
        // Distance is in miles.
        $radius = 3963.1676;
    } else {
        // distance is in km.
        $radius = 6378.1;
    }

    //	New latitude in degrees.
    $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));

    //	New longitude in degrees.
    $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));

    if ($return_as_array) {
        //  Assign new latitude and longitude to an array to be returned to the caller.
        $coord = array();
        $coord['lat'] = $new_latitude;
        $coord['lng'] = $new_longitude;
    } else {
        $coord = $new_latitude . "," . $new_longitude;
    }

    return $coord;
}

?>