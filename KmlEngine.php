<?php

// Based on
// SOFTWARE NAME: Thematic Mapping Engine
// SOFTWARE RELEASE: 1.6
// COPYRIGHT NOTICE: Copyright (C) 2008 Bjorn Sandvik,
//                   bjorn@thematicmapping.org
// SOFTWARE LICENSE: GNU General Public License version 3 (GPLv3)
// NOTICE: >
//  This program is free software; you can redistribute it and/or
//  modify it under the terms of version 3 of the GNU General
//  Public License as published by the Free Software Foundation.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//  http://www.gnu.org/licenses/
//
//


class KmlMaker {

    public $dataStore;
    public $coordDecimals = 6;
    public $engine = "";
    public $logoline = "files/balloonlogo.png";
    public $logo = 'files/logo.png';
    // Parameters
    public $barSize = 50000;
    public $mapDescription;//name of location
    public $mapSource = "Twitter";
    public $mapTitle = "Twitter Trends";
    public $symbolType;                 // image / polygon // collada
    public $symbolShape;
    // Colour parameters
    public $colourType = 'scale';         // scale / single
    //public $colourType = '';  
    public $startColour = 'FFFF99';
    public $endColour = 'FF6600';
    public $noDataColour = 'CCCCCC';
    public $colour = 'FF6600';
    public $opacity = 90;
    public $maxHeight = 2000000;  
    public $maxValue;
    public $minValue;
    public $kmlAlphaColour;

    function __construct($dataStore) {
        $this->dataStore = $dataStore;
        $this->minValue  = 100;
        $this->maxValue  = 100000;
        $this->maxHeight = 20000000;
        $this->barSize = 500;
        $this->colour = '';
        $this->colourType = 'scale';
        $this->opacity = 90;
        $this->kmlAlphaColour = 'FF';
        $this->coordDecimals = 6;
        $this->symbolShape = 'image';
        
    }

    //
    // Constructor
    // @access protected
    //
    function __deconstruct()
    {
        // What goes here?
    }
    
    //
    // Function
    // @access protected
    //
    public function getKML() {

        // Create KMZ archieve
        $file = "tmp/tme" . time() . ".kmz";
        $zip = new ZipArchive();
        if ($zip->open($file, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$file>\n");
        }

        $zip->addFile($this->logoline, 'files/balloonlogo.png');
        
        
        // KML header
        $kml .= "<?xml version='1.0' encoding='UTF-8'?>" . PHP_EOL
                . "<kml xmlns='http://www.opengis.net/kml/2.2' xmlns:atom='http://www.w3.org/2005/Atom'>" . PHP_EOL
                . "  <Document>" . PHP_EOL
                . "    <atom:author>" . PHP_EOL
                . "      <atom:name>Cps 630 Project</atom:name>" . PHP_EOL
                . "    </atom:author>" . PHP_EOL
                . "    <atom:link href='#' rel='related' />" . PHP_EOL
                . "    <name>$this->mapTitle</name>" . PHP_EOL
                . "    <open>1</open>" . PHP_EOL
                . "    <Snippet maxLines='1'>$this->mapSource</Snippet>" . PHP_EOL
                . "    <description><![CDATA[ $this->mapDescription <p>$this->mapSource</p>]]></description>" . PHP_EOL;

        // Add style for indicator balloon
        $kmlStyles = "    <Style id='balloonStyle'>" . PHP_EOL
                . "      <BalloonStyle>" . PHP_EOL
                . "        <text><![CDATA[" . PHP_EOL
                . "          <a href='#'><img src='files/balloonlogo.png'></a>" . PHP_EOL
                . "          <p><b><font size='+2'>$[name]</font></b></p>" . PHP_EOL
                . "          <p>$[description]</p>" . PHP_EOL
                . "        ]]></text>" . PHP_EOL
                . "      </BalloonStyle>" . PHP_EOL
                . "    </Style>" . PHP_EOL
                . "    <styleUrl>#balloonStyle</styleUrl>" . PHP_EOL;

        if ($this->colourType == 'scale') {
            // Need to run before getColourValue / getColourLegend / makeClasses
            self::makeColourScale();

            // Add colour legend to KMZ archieve
            if ($this->showLegend) {
                $zip->addFile(self::getColourLegend(), 'files/legend.png');
            }
            $kmlSingleColour = ''; // Colours needs to be defined for each feature
            //$kmlColour = self::rgb2bgr($this->noDataColour); // Not in use, only so the variable has a value
        } else {
            $kmlSingleColour = '<color>' . self::rgb2bgr($this->colour) . '</color>';
            //$kmlColour = self::rgb2bgr($this->colour);
        }

        // Add style for value placemarks
        if ($this->showLabel) {
            $kmlStyles .= "    <Style id='labelPlacemark'>" . PHP_EOL
                    . "      <IconStyle>" . PHP_EOL
                    . "        <scale>0.0</scale>" . PHP_EOL
                    . "      </IconStyle>" . PHP_EOL
                    . "      <LabelStyle>" . PHP_EOL
                    . "        <scale>1</scale>" . PHP_EOL
                    . "      </LabelStyle>" . PHP_EOL
                    . "    </Style>" . PHP_EOL;
        }

        // Define shared styles and legend
        $kmlStyles .= "    <Style id='sharedStyle'>" . PHP_EOL;

        $kmlStyles .= "      <PolyStyle>" . PHP_EOL
                . "        <fill>1</fill>" . PHP_EOL
                . "        <outline>0</outline>" . PHP_EOL
                . "        $kmlSingleColour" . PHP_EOL
                . "      </PolyStyle>" . PHP_EOL;

        $kmlStyles .= "      <BalloonStyle>" . PHP_EOL
                . "        <text><![CDATA[" . PHP_EOL
                . "          <a href='#'><img src='files/balloonlogo.png'></a>" . PHP_EOL
                . "          <p><b><font size='+2'>$[name]</font></b></p>" . PHP_EOL
                . "          <p>$this->mapTitle: $[Snippet]</p>" . PHP_EOL
                . "          <p>$this->mapDescription</p>" . PHP_EOL
                . "          <p>$this->mapSource</p>" . PHP_EOL
                . "          <p>$this->engine</p>" . PHP_EOL
                . "        ]]></text>" . PHP_EOL
                . "      </BalloonStyle>" . PHP_EOL
                . "    </Style>" . PHP_EOL;  // End of shared style

        $kmlFolder = "    <Folder>" . PHP_EOL
                . "      <name>Trends</name>" . PHP_EOL
                . "      <open>1</open>" . PHP_EOL;

        // Loop thorough all features (values without features will not be shown)
        foreach ($this->dataStore as $marker) {
            
            $longitude = $marker->longitude;
            $latitude = $marker->latitude;
            $name = $marker->trendName;
            $value = $marker->altitude;
            $colorVal = self::getKmlColour($marker->color) ;
            $visibility = 1;
            
            // Colour scale
            //hard-coded
            //$kmlColour = self::getColourValue($value, 'kml');         
            

            if ($value != null) {
                $altitude = intval($value * ($this->maxHeight / $this->maxValue));

                if ($this->colourType == 'scale') {
                    $kmlFeature = "          <Style>" . PHP_EOL
                            . "            <PolyStyle>" . PHP_EOL
                            . "              <color>$colorVal</color>" . PHP_EOL
                            . "            </PolyStyle>" . PHP_EOL
                            . "          </Style>" . PHP_EOL;
                }

                $kmlFeature .= self::kmlSymbolCalculator($longitude, $latitude, $this->barSize, 15, $altitude);
            }

            $kmlFeatures .= "        <Placemark>" . PHP_EOL
                    . "          <name>$name</name>" . PHP_EOL
                    . "          <visibility>$visibility</visibility>" . PHP_EOL
                    . "          <Snippet>($marker->trendNum)</Snippet>" . PHP_EOL
                    . "          <styleUrl>#sharedStyle</styleUrl>" . PHP_EOL
                    . $kmlFeature
                    . "        </Placemark>" . PHP_EOL;
        }// foreach features

        if ($this->showLabel) {
            $kmlLabels .= "        </Folder>";
            $kmlFolder .= $kmlLabels;
        }

        $kmlFolder .= $kmlFeatures;

        $kmlFolder .= "      </Folder>" . PHP_EOL;

        $kml .= $kmlStyles . $kmlFolder;

        $kml .= "  </Document>" . PHP_EOL
                . "</kml>" . PHP_EOL;
        //echo($kml);
        
        // Add kml to archieve
        $zip->addFromString("doc.kml", $kml);

        // Create logo with title and source and add to archieve
        if ($this->showTitle) {
            $zip->addFile(self::mapTitleImage(), 'files/brand.png');
        }
        else {
            $zip->addFile($this->logo, 'files/brand.png');
        }


        $zip->close();

        return $file;
    }

    // Function
    // @access protected
    //
    // This function is based on code developed by "TJ":
    // http://bbs.keyhole.com/ubb/showflat.php?Cat=&Board=SupportKML&Number=166379&Searchpage=1&Main=166379&Words=calculate+TJ1&topic=&Search=true
    //
    function kmlSymbolCalculator($longitude, $latitude, $distance, $points, $altitude) {
        $EARTH_RADIUS_EQUATOR = 6378140.0;
        $RADIAN = 180 / pi();

        $long = $longitude;
        $lat = $latitude;

        $long = $long / $RADIAN;
        $lat = $lat / $RADIAN;
        $f = 1 / 298.257;
        $e = 0.08181922;

        $kml = '          <Polygon>' . PHP_EOL
                . '            <outerBoundaryIs>' . PHP_EOL
                . '              <LinearRing>' . PHP_EOL
                . '                <coordinates>';

        //for ( $bearing = 0; $bearing <= 360; $bearing += 360/$points) {
        // Changed start bearing beacuse of square orientation
        for ($bearing = 45; $bearing <= 405; $bearing += 360 / $points) {

            $b = $bearing / $RADIAN;

            $R = $EARTH_RADIUS_EQUATOR * (1 - $e * $e) / pow((1 - $e * $e * pow(sin($lat), 2)), 1.5);
            $psi = $distance / $R;
            $phi = pi() / 2 - $lat;
            $arccos = cos($psi) * cos($phi) + sin($psi) * sin($phi) * cos($b);
            $latA = (pi() / 2 - acos($arccos)) * $RADIAN;

            $arcsin = sin($b) * sin($psi) / sin($phi);
            $longA = ($long - asin($arcsin)) * $RADIAN;

            $kml .= " " . round($longA, $this->coordDecimals) . "," . round($latA, $this->coordDecimals);
            if ($altitude)
                $kml .= "," . $altitude;
        }

        $kml .= '                </coordinates>' . PHP_EOL
                . '              </LinearRing>' . PHP_EOL
                . '            </outerBoundaryIs>' . PHP_EOL;

        if ($altitude) {
            $kml .= '            <extrude>1</extrude>' . PHP_EOL
                    . '            <altitudeMode>absolute</altitudeMode>' . PHP_EOL;
        }

        $kml .= '          </Polygon>' . PHP_EOL;

        return $kml;
    }

    // Generates a colour scale
    function makeColourScale() {

        // Extract red/green/blue decimal values from hexadecimal colours
        $this->startColourRGB = array(hexdec(substr($this->startColour, 0, 2)),
            hexdec(substr($this->startColour, 2, 2)),
            hexdec(substr($this->startColour, 4, 2)));

        $this->endColourRGB = array(hexdec(substr($this->endColour, 0, 2)),
            hexdec(substr($this->endColour, 2, 2)),
            hexdec(substr($this->endColour, 4, 2)));

        // Calculate the change value for red/green/blue
        $this->deltaColourRGB = array($this->endColourRGB[0] - $this->startColourRGB[0],
            $this->endColourRGB[1] - $this->startColourRGB[1],
            $this->endColourRGB[2] - $this->startColourRGB[2]);
    }

    
    function getColourValue($value, $format) {
        $red = $this->startColourRGB[0] + ($this->deltaColourRGB[0] / ($this->maxValue - $this->minValue)) * ($value - $this->minValue);
        $green = $this->startColourRGB[1] + ($this->deltaColourRGB[1] / ($this->maxValue - $this->minValue)) * ($value - $this->minValue);
        $blue = $this->startColourRGB[2] + ($this->deltaColourRGB[2] / ($this->maxValue - $this->minValue)) * ($value - $this->minValue);

        if ($format == 'kml') {
            $colour = sprintf('%02X%02X%02X%02X', $this->kmlAlphaColour, $blue, $green, $red);
        } else { // Hex colour
            $colour = sprintf('%02X%02X%02X', $red, $green, $blue);
        }

        return $colour;
    }
    
    // Generates KML colour
    function rgb2bgr($rgb)
    {
        $colour = dechex($this->kmlAlphaColour) . substr($rgb, -2) . substr($rgb, 2, 2) . substr($rgb, 0, 2);
        return $colour;
    }

    
    function getKmlColour($hexColor){
        $blue = substr($hexColor, -2);
        $green = substr($hexColor, 2, 2);
        $red = substr($hexColor, 0, 2);
        $kmlHexcolor = $this->kmlAlphaColour . $blue .$green . $red;
        return $kmlHexcolor;
    }
}

?>
