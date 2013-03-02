<?php

function generateRandomColor()
{
  $randomcolor = 'ff' . dechex(rand(0,10000000));
  if (strlen($randomcolor) != 8)
  {
    $randomcolor = str_pad($randomcolor, 10, '0', STR_PAD_RIGHT);
    $randomcolor = substr($randomcolor,0,8);
  }
  return $randomcolor;
}

class Placemark
{
  public $id;
  public $name;
  public $color;
  public $lat;
  public $lon;
  public $height;
  public $coordinates;
  public $midCoordinate;
  public $LENGTH_OF_BLOCK = 0.0003;
  public $mLENGTH_OF_BLOCK = -0.0003;
  public $MIDPOINT_OF_BLOCK = 0.00002;
  public $coordDecimals  = 6;
  public static $rankOfTrend = 1;
  
      // Function
    // @access protected
    //
    // This function is based on code developed by "TJ":
    // http://bbs.keyhole.com/ubb/showflat.php?Cat=&Board=SupportKML&Number=166379&Searchpage=1&Main=166379&Words=calculate+TJ1&topic=&Search=true
    //
  function __construct($id = NULL, $name = NULL, $color = NULL, $lon = NULL, $lat = NULL, $height= NULL, $flag)
  {
  	$RADIAN = 180 / pi();

  	
  	
	$this->id = (is_null($id)?"No ID":$id);
	
	$this->name = (is_null($name)?"Unnamed":$name.": ".$height);
	$this->color = (is_null($color)?generateRandomColor():$color);
	//$this->lat = (is_null($lat)?"43.65".strval(rand(00,99)):$lat/$RADIAN);
        //$this->lon = (is_null($lon)?"-79.37".strval(rand(00,99)):$lon/$RADIAN);
        $this->lat = $lat/$RADIAN;
        $this->lon = $lon/$RADIAN;
	//$this->height = (is_null($height)?rand(50,399):$height*5.0);
	$this->height = $height*9;
	
	if ($flag == 0) // if hashtags and not trends, then we are changing the label.
	{
	$this->name = (is_null($name)?"Unnamed":$name.": ".$height);
	}else if ($flag == 1)
	{
		$this->name = (is_null($name)?"Unnamed":$name.", Rank: ".self::$rankOfTrend);
		self::$rankOfTrend =  self::$rankOfTrend+1;
	}
	
	
	//Values for coordinates forumla
	$points = 45;
	$EARTH_RADIUS_EQUATOR = 6378140.0;
        $RADIAN = 180 / pi();
        $f = 1 / 298.257;
        $e = 0.08181922;
        $distance = 100;


	for ($bearing = 45; $bearing <= 405; $bearing += 360 / $points) {

            $b = $bearing / $RADIAN;

            $R = $EARTH_RADIUS_EQUATOR * (1 - $e * $e) / pow((1 - $e * $e * pow(sin($this->lat), 2)), 1.5);
            $psi = $distance / $R;
            $phi = pi() / 2 - $this->lat;
            $arccos = cos($psi) * cos($phi) + sin($psi) * sin($phi) * cos($b);
            $latA = (pi() / 2 - acos($arccos)) * $RADIAN;

            $arcsin = sin($b) * sin($psi) / sin($phi);
            $longA = ($this->lon - asin($arcsin)) * $RADIAN;

            $this->coordinates .= " " . round($longA, $this->coordDecimals) . "," . round($latA, $this->coordDecimals);
            if ($this->height)
                $this->coordinates .= "," . $this->height;
        }
        
        $this->midCoordinate = "\n\t\t" . $this->lon * $RADIAN . "," . $this->lat* $RADIAN . "," . ($this->height+80);
/*
$this->coordinates =
              "\n\t\t" . $this->lon . "," . $this->lat . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 2, 4) . "," . bcadd($this->lat, $this->LENGTH_OF_BLOCK * 2 , 4) . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 4 , 4) . "," . bcadd($this->lat, $this->LENGTH_OF_BLOCK * 2 , 4) . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 6 , 4) . "," . $this->lat . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 6 , 4) . "," . bcadd($this->lat, $this->mLENGTH_OF_BLOCK * 2 , 4) . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 4 , 4) . "," . bcadd($this->lat, $this->mLENGTH_OF_BLOCK * 4 , 4) . "," . $this->height .
              "\n\t\t" . bcadd($this->lon, $this->LENGTH_OF_BLOCK * 2 , 4) . "," . bcadd($this->lat, $this->mLENGTH_OF_BLOCK * 4 , 4) . "," . $this->height .
              "\n\t\t" . $this->lon . "," . bcadd($this->lat, $this->mLENGTH_OF_BLOCK * 2, 4) . "," . $this->height .
              "\n\t\t" . $this->lon . "," . $this->lat . "," . $this->height;
*/
	
  }
}
?>