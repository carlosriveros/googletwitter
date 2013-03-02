<?php

class LinearRing
{
	public $coordinatesbr;
	public $coordinates;
	public $lat;
	public $lon;
	public $size;

	
	function __construct($coordinates, $size)
   {
	 $this->coordinatesbr = explode(",", $coordinates);
	 $this->lon=floatval($this->coordinatesbr[0]);
	 $this->lat=floatval($this->coordinatesbr[1]);
	 $this->size = $size;

	 $this->coordinates =  	"\n\t\t" . $this->lon . "," . $this->lat . "," . $this->size .
	 						"\n\t\t" . bcadd($this->lon, 0.0009, 4) . "," . $this->lat . "," . $this->size .
	 						"\n\t\t" . bcadd($this->lon, 0.0009, 4) . "," . bcadd($this->lat, 0.0009, 4) . "," . $this->size .
	 						"\n\t\t" . $this->lon . "," . bcadd($this->lat, 0.0009, 4) . "," . $this->size .
	 					 	"\n\t\t" . $this->lon . "," . $this->lat . "," . $this->size . "\n\t" ;
	 return array("0","2","6","8");
   }
}

class Polygon
{
	public $extrude;
	public $altitudeMode;
	public $linearRing;
	
	function __construct($extrude, $altitudeMode, $linearRing)
   {
	 $this->extrude = $extrude;
	 $this->altitudeMode = $altitudeMode;
	 $this->linearRing = $linearRing;
   }
}


class Placemark
{
  public $id;
  public $name;
  public $color;
  public $polygon;
 
  function __construct($id, $name, $color, $polygon)
  {
	$this->id = $id;
	$this->name = $name;
	$this->color = $color;
	$this->polygon = $polygon;
  }

}
?>