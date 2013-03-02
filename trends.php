<?php ?>
<html>
    <head>
        <title>CPS 630 Project</title>        
        <meta http-equiv="imagetoolbar" content="no" />
        <link rel="stylesheet" href="app_content/css/layout.css" type="text/css" /> 
        <script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>       
        <script type="text/javascript" src="js/geo.main.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"> </script>
        <script type="text/javascript">
            var file;
            var ge;
            google.load("earth", "1");

            function init() {
                google.earth.createInstance('featured_slide_', initCB, failureCB);
            }

            function initCB(instance) {
                ge = instance;
                ge.getWindow().setVisibility(true);
                // add a navigation control
                ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);

                // add some layers
                ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
                ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);
                
                //addKmlFromUrl('http://localhost:90/GeoTwitterProject/kml/output.kml');
                //link.setHref("http://localhost/kml/uk-emissions.kml");
                
                //addKmlFromUrl(kmlUrl);
            }

            function failureCB(errorCode) {
            }

            google.setOnLoadCallback(init);
      
            function addKmlFromUrl(kmlUrl) {
                var link = ge.createLink('');
                link.setHref(kmlUrl);

                var networkLink = ge.createNetworkLink('');
                networkLink.setLink(link);
                networkLink.setFlyToView(true);

                ge.getFeatures().appendChild(networkLink);
            }
			
            /*google.earth.addEventListener(TheWorstFeeling, 'click', function(event) {
                                alert('placemark bounding box clicked');
                        });*/

        </script>

    </head>
<!--    <center> <div id="map3d" style="height: 400px; width: 600px;"></div> </center>-->

    <body id="top">
        <div class="wrapper col1">
            <div id="header">
                <div id="topnav">
                    <ul>
                        <li class="last"><a href="gallery.html">CONTACT</a></li>
                        <li><a href="#">ABOUT</a>
                            <ul>
                                <li><a href="#">Link 1</a></li>
                                <li><a href="#">Link 2</a></li>
                                <li><a href="#">Link 3</a></li>
                            </ul>
                        </li>
                        <li><a href="full-width.html">TUTORIAL</a></li>        
                        <li class="active"><a href="index.html">Homepage</a></li>
                    </ul>
                </div>
                <div class="fl_left">
                    <h1 style="width:225px;float:left"><input id="place" type="textbox" style="width:200px"></h1>
                    <p style="width:225px;float:left">Text box to search for top trends in a city</p>
                    <button onClick='lookup_woeid()'>Search</button>
                </div>
                <br class="clear" />
            </div>
        </div>
        <!-- ####################################################################################################### -->
        <div class="wrapper col2">
            <div id="featured_slide_">
                <ul id="featured_slide_Content">


                    <li class="clear featured_slide_Image">
                        <!-- Leave This Empty -->
                    </li>
                </ul>
            </div>
        </div>
        <!-- ####################################################################################################### -->
        <div class="wrapper col5">
            <div id="footer">    
                <p class="fl_right">Powered by <a href="#" title="">....</a></p>
                <div id = 'output'/></div>
            <br class="clear" />
        </div>
    </div>
</body>
</html>
