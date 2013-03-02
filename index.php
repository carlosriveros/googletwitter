 <?php

 //session_start();
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


    <head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

        <title>Twitter Overlay</title>

        <meta http-equiv="imagetoolbar" content="no" />

        <script src="http://www.google.com/jsapi?key=ABQIAAAAuPsJpk3MBtDpJ4G8cqBnjRRaGTYH6UMl8mADNa0YKuWNNa8VNxQCzVBXTx2DYyXGsTOxpWhvIG7Djw" type="text/javascript"></script>

   	  <link rel="stylesheet" href="CSS/main.css">

        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>


        <script type="text/javascript">


        var ge;

        var city,since,tm,limit;
        var cityPattern = /[A-Z,a-z]/;
        var numPattern = /^[0-9]+$/;
        var errorMsg = "";

		<!--GOOGLE EARTH-->
		google.load("earth", "1");

		function init() {
			google.earth.createInstance('map3d', initCB, failureCB);
		}

		function initCB(instance) {
			ge = instance;
			ge.getWindow().setVisibility(true);


			// add a navigation control
			ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);

			// add some layers
			ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
			ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);




			document.getElementById('installed-plugin-version').innerHTML = ge.getPluginVersion().toString();

			//addKmlFromUrl('http://carloseriveros.com/load.kml');


		}

		function failureCB(errorCode) {
		}

		google.setOnLoadCallback(init);


		function finished(object) {
			//initially remove kml
			//ge.getFeatures().removeChild(kmlObject);

			if (!object) {
			// wrap alerts in API callbacks and event handlers
			// in a setTimeout to prevent deadlock in some browsers
			setTimeout(function() {
			alert('Bad or null KML.');
			}, 0);
			return;
			}


        		ge.getFeatures().appendChild(object);
        	}

		function addKmlFromUrl(kmlUrl) {

			google.earth.fetchKml(ge,kmlUrl,finished);

		}




		<!--COOKIES-->
		function createCookie(name,value,days){
			if (days){
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();

			}
			else var expires = "";
				document.cookie = name+"="+value+expires+"; path=/";

		}

		function readCookie(name)
		{
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0)
					return c.substring(nameEQ.length,c.length);
			}
			return null;
		}

		function eraseCookie(name)
		{
			createCookie(name,"",-1);
		}

		function info()
		{
			document.getElementById("Location").value = readCookie("Location");
			document.getElementById("Since").value = readCookie("Since");
			document.getElementById("Time").value = readCookie("Time");
			document.getElementById("Limit").value = readCookie("Limit");
		}

		<!--ERROR HANDLING OF FORM-->
		function runProgram()
		{

			city = document.getElementById("Location").value;
			since= document.getElementById("Since").value;
			tm = document.getElementById("Time").value;
			limit= document.getElementById("Limit").value;

			if(!cityPattern.test(city) || city=="Location (City)"){
				errorMsg+="Error: Please enter a Location.\n";
			}
			if(document.getElementById("rad-tweet1").checked){
				if( tm=="MINUTE" && (since=="Since (1-60,1-24, 1-7)" || since<1 ||  since>60 || !numPattern.test(since) )){

					errorMsg+="Error: Minute incorrect format (1-60).\n";
				}
				if( tm=="HOUR" && (since=="Since (1-60,1-24, 1-7)" || since<1 ||  since>24 || !numPattern.test(since))){

					errorMsg+="Error: Hour incorrect format (1-24).\n";
				}
				if( tm=="DAY" && (since=="Since (1-60,1-24, 1-7)" ||  since>7 ||  since<1 || !numPattern.test(since))){

					errorMsg+="Error: Day incorrect format or empty (1-7).\n";
				}
				if( tm=="WEEK" && (since=="Since (1-60,1-24, 1-7)" || since!=1 || !numPattern.test(since))){

					errorMsg+="Error: Week can only be '1'.\n";
				}
				if( since==""){

					errorMsg+="Error: Select a number associated with the time unit.\n";
				}
				if( tm==""){

					errorMsg+="Error: Select Minute, Hour, Day or Week.\n";
				}
				if(limit>30 || limit<1 || !numPattern.test(limit) || limit=="Tweet results to Show (1-30)"){

					errorMsg+="Error: Results (1-30).\n";
				}
			}

			//If no errors, submit() and save cookies of info
			if(errorMsg==""){
				createCookie("Location",document.getElementById("Location").value,1);
				createCookie("Since",document.getElementById("Since").value,1);
				createCookie("Time",document.getElementById("Time").value,1);
				createCookie("Limit",document.getElementById("Limit").value,1);
				return true;
			}
			else{
				alert(errorMsg);
				errorMsg="";
				return false;
			}

		}


		<!--AJAX FOR PROGRAM-->
		var data_array;
		var arry;
		var BeginTime="";

		function RemoveAllFeatures()
		{
			var features = ge.getFeatures();
			while (features.getLastChild() != null)
			{
				features.removeChild(features.getLastChild());
			}
		}



		function modDom(data)
		{


		        //Add left divs for interactivity
			$('#left-side').remove();
			$('#map3d').css("width","900");
			$('<div id = "left-side" style="height: 450px; width: 200px;"></div>').insertBefore('.wrappercol2');

			//$('<div id = "hashtagsTitle">Hashtags (Click one)<br/> Sorted By Popularity (DESC):</div>').appendTo('#left-side');
			$('<div id = "hashtagsTitle"></div>').appendTo('#left-side');

			$('<div id = "hashtags" style="height: 255px; width: 200px; overflow-y:auto;"></div>').insertAfter('#hashtagsTitle');
			$('<br id="break"></br>').insertAfter('#hashtags');
			$('<div id = "tweetsTitle"></div>').insertAfter('#break');
			//$('<div id = "tweetsTitle">Tweets with clicked Hashtag:</div>').insertAfter('#break');
			$('<div id = "tweets" style="height: 255px; width: 200px; overflow-y:auto; overflow-x:auto;"></div>').insertAfter('#tweetsTitle');

			//alert(data);

			var r10 = document.getElementsByClassName('radhash');
			var r1 = r10[0];
			var r20 = document.getElementsByClassName('radtrends');
			var r2 = r20[0];
			if(r1.checked)
			{
 			var size = data.length;
 			//for(var i in data)
 			for(var i = 0; i < size - 1; i++)
			{


			 	//add hashtag data to list of hashtags
			 $('<p id =\"ahashtag\" style="cursor: pointer; cursor: hand;" id = \"'+ i +'\">'+data[i]+'</p>').appendTo('#hashtags');
			 }

			}

			else if
			(r2.checked)
			{
			var size = data.length;
 			//for(var i in data)
 			for(var i = 0; i < size - 1; i++)
			{


			 	//add hashtag data to list of hashtags
			 $('<p id =\"ahashtag\" style="cursor: pointer; cursor: hand;" id = \"'+ i +'\">'+data[i]+'</p>').appendTo('#hashtags');
			 }

			}




			//loop through data and put into Hashtags div


			//click listener. Find the clicked value
			$("p").click(function(){
				//var tweetSelected = this.innerHTML.split(",");

				//new ajax call to get tweets containing hashtag
				var radioValue;
				if(document.getElementById("rad-tweet1").checked)
					radioValue = "getHashtags";
				else if(document.getElementById("rad-tweet2").checked)
					radioValue = "getTrends";

				$.ajax({

					url: "indexGetTweets.php",
					data: {
					    Tweet: this.innerHTML,
					    RadTweet: radioValue,
					    BeginTime: BeginTime,
					    Location: document.getElementById("Location").value,
					    Since: document.getElementById("Since").value,
					    TimeUnit: document.getElementById("Time").value
					},
					success:function(data, textStatus, jqXHR){
						//go to location


						if(data == "")
							data="No results found in our database.";

						$('.tweet').remove();
						$('#tweets').empty();
						$('#tweets').append("<p class =\"tweet\">" + data + " </p>");
					}
				});

			});

		}

		$(document).ready(function()
		{

			$('.radtrends').click(function(){
				$('#Since').hide('fast');
				$('#since-label').hide('fast');
				$('#Time').hide('fast');
				$('#time-label1').hide('fast');
				$('#time-label2').hide('fast');
				$('#Limit').hide('fast');
				$('#limit-label').hide('fast');

			});

			$('.radhash').click(function(){


				$('#Since').show('fast');
				$('#since-label').show('fast');
				$('#Time').show('fast');
				$('#time-label1').show('fast');
				$('#time-label2').show('fast');
				$('#Limit').show('fast');
				$('#limit-label').show('fast');

			});



    			$('#mainform').submit(function(){

				if(runProgram()){

					//addKmlFromUrl("<?php  echo 'http://carloseriveros.com/' . $_SESSION['sessionID'] . '.kml' ; ?>");
					//alert("<?php  echo 'http://carloseriveros.com/' . $_SESSION['sessionID'] . '.kml' ; ?>");
					//Loading div. To be hidden upon sucessful ajax. Disable submit button
					document.getElementById("Loading").style.visibility="visible";
					document.getElementById("submitButton").disabled=true;

					//alert("Ajax");
					$.ajax({
		 				 url: "indexProgram.php",
		 				 type: 'POST',
		 				 data: $(this).serialize(),

		 				 success:function(data, textStatus, jqXHR){

		 				 	//addKmlFromUrl('http://carloseriveros.com/test3.kml');
		 				   if(data[1] == "<"){
		 				   alert("No trends available for " + city + ".\nPlease try a different location");
		 				   };
		 				 	//take away loading div and reenable submit.
		 				 	document.getElementById("Loading").style.visibility="hidden";
							document.getElementById("submitButton").disabled=false;


		 				 	var arr = data.split(",-,");

		 				 	BeginTime = arr[3];

		 				 	if(!(/^\s*$/).test(arr[0])) {

		 				 	// Create a new LookAt.
							var lookAt = ge.createLookAt('');

							//get hastags and count
							data_array = arr[4].split("|");

							var sessionID = data.split("%")[1];
							//alert(sessionID);
							RemoveAllFeatures();
							var url = 'http://carloseriveros.com/tmp/' + sessionID + '.kml';
							//var url = 'http://carloseriveros.com/load.kml';
							addKmlFromUrl(url);


							// Set the position values.
							lookAt.setLatitude(parseFloat(arr[0]));
							lookAt.setLongitude(parseFloat(arr[1]));
							lookAt.setTilt(50.0);
							lookAt.setRange(9000.0); //default is 0.0

							// Update the view in Google Earth.
							ge.getView().setAbstractView(lookAt);
							}
							else{

							alert("Location does not exist. Please try again with an existing Location")

							}
						},
						complete : function(){
							if($.browser.msie){

							}
							else{

							modDom(data_array);
							}
						}
					});


				}


				//doesn't refresh pag
				return false;
			});



 		});

 		//alert(data_array[0]);


	</script>
    </head>


<body bgcolor="0075EB" onload="info()">
<div id = "wrapper">
  	<div class = "user-form" >

	     <form  action="" id="mainform" method="POST" >
		<div id = "radio-buttons">
			<input id="rad-tweet1" type="radio" name="rad-tweet" value="getHashtags" class="radhash" checked/>&nbsp;<span id="rad-label">Get Hashtags</span>&nbsp;
			<input id="rad-tweet2" type="radio" name="rad-tweet" value="getTrends" class="radtrends" />&nbsp;<span id="rad-label">Get Trends</span>&nbsp;
		</div>
	      	<label class="label" for="Location" >Location</label>
	      	<input class ="text-box" type="text" name="Location" id="Location"/>
	      	<label id="since-label" class="label" for="Since" >&nbsp;&nbsp;&nbsp;Since (Ex: 5 Hours)</label>
	      	<input  class ="text-box" type="text" name="Since" id="Since" />

	      	<select name="Time" id="Time" style="width:80px;">
	      		<option value="MINUTE">Minute(s)</option>
	      		<option value="HOUR">Hour(s)</option>
	      		<option value="DAY">Day(s)</option>
	      		<option value="WEEK">Week</option>
	      	</select>
	      	<label id="time-label1" class="label" for="Time" >&nbsp;&nbsp;&nbsp;</label>
	      	 <label id="time-label2" class="label" for="Limit" >Results to Output (1-30)&nbsp;</label>

	      	<input class ="text-box" type="text" name="Limit" id="Limit" />
	      	<label id="limit-label" class="label" for="Limit" >&nbsp;&nbsp;&nbsp;</label>
	      	<input class ="submit-button" type="submit" id="submitButton" value="Get Results" style="width:95px;" />
	      </form>



	    <label class="label" id="Loading" style="visibility:hidden;"><br/>Loading. Please Wait...</label>

	</div>
	<!-- ####################################################################################################### -->
	<div id="center" >
		<div class="wrappercol2">

			<div id="map3d" style="height: 530px; width: 1100px; "></div>
			<div>Installed Plugin Version: <span id="installed-plugin-version" style="font-weight: bold;">Loading...</span></div>


		</div>
	</div>


	<!-- <div class="wrapper col5">
	 <div id="footer">
	    <p class="fl_right">Powered by <a href="#" title="">....</a></p>
	    <br class="clear" />
	</div>
	-->

</div>
</body>
</html>