var file = '';
var latitude;
var longitude;
var woeid;

function lookup_woeid() {    
    
    var place = encodeURI($('#place').val());
	
    $.getJSON(
        'http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22'+place+'%22&format=json',
        function(data) {
            $('#output').html('');
            if (data.query.count == 0) {
                return false;
            } else if (data.query.count == 1) {
                var place = data.query.results.place;
                latitude = place.centroid.latitude;
                longitude = place.centroid.longitude;
                woeid = place.woeid;
                /* $('<tr><td>'+place.woeid+'</td><td>'+place.name+'</td><td>'+place.admin1.content+'</td></tr>')
            .appendTo('#output');*/
                getKmlFile();
            } else {
                var places = data.query.results.place;
                top_choice = places[0];
                latitude = top_choice.centroid.latitude;
                longitude = top_choice.centroid.longitude;
                woeid = top_choice.woeid; 
                /*$('<tr><td>'+top_choice.woeid+'</td><td>'+top_choice.name+'<td><td>'+top_choice.admin1.content+'</td></tr>')
            .appendTo('#output');*/
                getKmlFile();
            }
        });
}

function getKmlFile(){
    var params = "woeid=" + woeid + "&lat=" + latitude +"&lng=" + longitude;
    $.ajax({
        type: 'POST',
        url: 'KmlHandler.php',
        data: params,
        dataType: 'json',
        success: function(result){
             if(result.success == 'true'){
                 var fileName = result.file;
                 
                //kmlUrl = 'http://localhost:90/cps630Project/trends/' + fileName;
				kmlUrl = 'http://carloseriveros.com/' + fileName;
                //alert(kmlUrl);
                addKmlFromUrl(kmlUrl);
             }
            
           
        }
        
    });
    
}
