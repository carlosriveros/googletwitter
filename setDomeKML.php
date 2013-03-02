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

  public $RADIAN;

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

    $this->RADIAN = 180 / pi();


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
     $placemarkTag = $this->folTag->appendChild(
       $this->xmlDoc->createElement("Placemark"));

     $placemarkTag->appendChild(
       $this->xmlDoc->createAttribute("id"))->appendChild(
       $this->xmlDoc->createTextNode($place->id));

       $placemarkTag->appendChild(
         $this->xmlDoc->createElement("name", $place->name));

       $styTag = $placemarkTag->appendChild(
        $this->xmlDoc->createElement("Style"));

             $polstyTag = $styTag->appendChild(
              $this->xmlDoc->createElement("ModelStyle"));

                     $polstyTag->appendChild(
                      $this->xmlDoc->createElement("color", $place->color));

                     $polstyTag->appendChild(
                      $this->xmlDoc->createElement("outline", "0"));

       $modelTag = $placemarkTag->appendChild(
         $this->xmlDoc->createElement("Model"));

                 $modelTag->appendChild(
                   $this->xmlDoc->createElement("altitudeMode", "relativeToGround"));

                 $locTag = $modelTag->appendChild(
                  $this->xmlDoc->createElement("Location"));

                         $lonTag = $locTag->appendChild(
                          $this->xmlDoc->createElement("longitude", (($place->lon) * $this->RADIAN)));

                         $latTag = $locTag->appendChild(
                          $this->xmlDoc->createElement("latitude", (($place->lat) * $this->RADIAN)));

                         $altTag = $locTag->appendChild(
                          $this->xmlDoc->createElement("altitude", "0"));

       $scaleTag = $modelTag->appendChild(
        $this->xmlDoc->createElement("Scale"));

                        $xTag = $scaleTag->appendChild(
                          $this->xmlDoc->createElement("x", $place->height));

                        $yTag = $scaleTag->appendChild(
                          $this->xmlDoc->createElement("y", $place->height));

                        $zTag = $scaleTag->appendChild(
                          $this->xmlDoc->createElement("z", $place->height));

        $linkTag = $modelTag->appendChild(
        $this->xmlDoc->createElement("Link"));

                        $hrefTag = $linkTag->appendChild(
                          $this->xmlDoc->createElement("href", "files/pyramid/object1.dae"));

       $placemarkTag = $this->folTag->appendChild(
         $this->xmlDoc->createElement("Placemark"));

       $placemarkTag->appendChild(
         $this->xmlDoc->createElement("name", $place->name));

       $styTag = $placemarkTag->appendChild(
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

       $pntTag = $placemarkTag->appendChild(
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