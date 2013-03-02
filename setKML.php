 <?php

 //session_start();
 require("class.php");
 require("GenerateLoadKML.php");


 class GenerateKML
 {
  public $NAME;
  public $DESCRIPTION;
  public $LONG;
  public $LAT;
  public $ALT;
  public $HEAD;
  public $TILT;
  public $RANGE;
  public $PLACEMARKSARRAY;
  public $folTag;
  public $xmlDoc;

  function __construct($NAME = NULL, $DESCRIPTION = NULL, $LONG = NULL, $LAT = NULL, $ALT = NULL, $HEAD = NULL, $TILT = NULL, $RANGE = NULL)
  {

    $this->NAME = (is_null($NAME)?"Building and Wall":$NAME);
    $this->DESCRIPTION = (is_null($DESCRIPTION)?"Building and Wall share textures":$DESCRIPTION);
    $this->LONG = (is_null($LONG)?"-79.37380981445312":$LONG);
    $this->LAT = (is_null($LAT)?"43.656211853027344":$LAT);
    $this->ALT = (is_null($ALT)?"311":$ALT);
    $this->HEAD = (is_null($HEAD)?"-40.34248730008207":$HEAD);
    $this->TILT = (is_null($TILT)?"34.9531454664821":$TILT);
    $this->RANGE = (is_null($RANGE)?"50.90180128676323":$RANGE);


    //create the xml document
    $this->xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $kml = $this->xmlDoc->appendChild($this->xmlDoc->createElementNS('http://www.opengis.net/kml/2.2', 'kml'));

    //create the root element
    $this->folTag = $kml->appendChild(
      $this->xmlDoc->createElement("Folder"));

    $this->folTag->appendChild(
      $this->xmlDoc->createElement("name",  $this->NAME));

    $this->folTag->appendChild(
      $this->xmlDoc->createElement("description", $this->DESCRIPTION));

    $lokTag =  $this->folTag->appendChild(
      $this->xmlDoc->createElement("Camera"));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("longitude", $this->LONG));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("latitude", $this->LAT));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("altitude", $this->ALT));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("heading", $this->HEAD));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("tilt", $this->TILT));

    $lokTag->appendChild(
      $this->xmlDoc->createElement("range", $this->RANGE));
  }

  function generatePlacemarks($placemarksArray = NULL)
  {
    $this->PLACEMARKSARRAY =(is_null($placemarksArray)?$this->PLACEMARKSARRAY:$placemarksArray);

    foreach( $this->PLACEMARKSARRAY as $place)
    {
     $tutTag = $this->folTag->appendChild(
       $this->xmlDoc->createElement("Placemark"));

     $tutTag->appendChild(
       $this->xmlDoc->createAttribute("id"))->appendChild(
       $this->xmlDoc->createTextNode($place->id));

	$position = strpos($place->name, "&");
	
	 
	 $newstring = substr($place->name, 0, $position)."".substr($place->name, $position +1);
	 if($position == "")
	 {
	 $newstring =$place->name;
	 }
       $tutTag->appendChild(
         $this->xmlDoc->createElement("name", $newstring));

       $styTag = $tutTag->appendChild(
        $this->xmlDoc->createElement("Style"));

       $polstyTag = $styTag->appendChild(
        $this->xmlDoc->createElement("PolyStyle"));

       $polstyTag->appendChild(
        $this->xmlDoc->createElement("color", $place->color));

       $polstyTag->appendChild(
        $this->xmlDoc->createElement("outline", "0"));

       $polTag = $tutTag->appendChild(
         $this->xmlDoc->createElement("Polygon"));

       $polTag->appendChild(
         $this->xmlDoc->createElement("extrude", "1"));

       $polTag->appendChild(
         $this->xmlDoc->createElement("altitudeMode", "relativeToGround"));

       $outTag = $polTag->appendChild(
        $this->xmlDoc->createElement("outerBoundaryIs"));

       $linTag = $outTag->appendChild(
        $this->xmlDoc->createElement("LinearRing"));

       $linTag->appendChild(
        $this->xmlDoc->createElement("coordinates", $place->coordinates));

       $tutTag = $this->folTag->appendChild(
         $this->xmlDoc->createElement("Placemark"));

	$position = strpos($place->name, "&");
	 $newstring = substr($place->name, 0, $position)."".substr($place->name, $position +1);
	  if($position == "")
	 {
	 $newstring =$place->name;
	 }

       $tutTag->appendChild(
         $this->xmlDoc->createElement("name",  $newstring));

       $styTag = $tutTag->appendChild(
        $this->xmlDoc->createElement("Style"));

       $polstyTag = $styTag->appendChild(
        $this->xmlDoc->createElement("LabelStyle"));

       $polstyTag->appendChild(
        $this->xmlDoc->createElement("scale", "0.75"));

       $polstyTag->appendChild(
        $this->xmlDoc->createElement("color", "FF00FF77"));

       $polstyTag = $styTag->appendChild(
        $this->xmlDoc->createElement("IconStyle"));

       $polstyTag->appendChild(
        $this->xmlDoc->createElement("scale", "0"));

       $pntTag = $tutTag->appendChild(
         $this->xmlDoc->createElement("Point"));

       $pntTag->appendChild(
        $this->xmlDoc->createElement("coordinates", $place->midCoordinate));

       $pntTag->appendChild(
        $this->xmlDoc->createElement("altitudeMode", "absolute"));
     }
   }

   function export()
   {
   //header("Content-Type: text/plain");
   //header('Content-type: application/vnd.google-earth.kml+xml');

    //make the output pretty



    //session_destroy();
    $this->xmlDoc->formatOutput = true;
    $this->xmlDoc->preserveWhiteSpace = false;


     $this->xmlDoc->saveXML();

     $loadKML = new GenerateLoadKML($this->NAME);
     $this->xmlDoc->save("tmp/".$this->NAME . ".kml");
     //$this->xmlDoc->save("test3.kml");

     }
 }

 ?>