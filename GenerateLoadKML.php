<?php
class GenerateLoadKML
{
	public $xmlDoc;
	public $NAME;

	function __construct($NAME = NULL)
	{
		$this->NAME = (is_null($NAME)?"test3":$NAME);

		$this->xmlDoc = new DOMDocument('1.0', 'UTF-8');
    	$kml = $this->xmlDoc->appendChild($this->xmlDoc->createElementNS('http://www.opengis.net/kml/2.2', 'kml'));

		$docTag = $kml->appendChild(
		$this->xmlDoc->createElement("Document"));

		$netTag = $docTag->appendChild(
		$this->xmlDoc->createElement("NetworkLink"));

		$linTag = $netTag->appendChild(
		$this->xmlDoc->createElement("Link"));

		$linTag->appendChild(
		$this->xmlDoc->createElement("href",  "http://carloseriveros.com/twitter360/tmp/" . $this->NAME . ".kml"));

/*		$linTag->appendChild(
		$this->xmlDoc->createElement("refreshMode",  "onInterval"));

		$linTag->appendChild(
		$this->xmlDoc->createElement("refreshInterval",  "1"));*/


		//header("Content-Type: text/plain");
  		$this->xmlDoc->formatOutput = true;
     	$this->xmlDoc->preserveWhiteSpace = false;
     	$this->xmlDoc->saveXML();
		$this->xmlDoc->save("load2.kml");
	}
}
//$test = new GenerateLoadKML("testRAZ");
?>
