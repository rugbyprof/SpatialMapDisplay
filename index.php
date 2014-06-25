<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polygon</title>
    <style>
		section {
			width: 90%;
			height: 600px;
			background: #C0C0C0;
			margin: auto;
			padding: 5px;
		}
		div#map-canvas {
			width: 80%;
			height: 600px;
			float: left;
		}
		div#form-stuff {
			margin-left: 15%;
			padding: 20px;
			height: 600px;
		}
    </style>
    <!-- Include Google Maps Api to generate maps -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    
    <!-- Include Jquery to help with simplifying javascript syntax  -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>

	var map;
	var markers = [];
	var polygons = [];

	//Runs when page is done loading
	function initialize() {
	  console.log($('#volcanoes').is(':checked'));
	  console.log($('#earthquakes').is(':checked'));
	  //Javascript object to help configure google map.
	  var mapOptions = {
		zoom: 4,
		center: new google.maps.LatLng(39.707, -101.503),
		mapTypeId: google.maps.MapTypeId.TERRAIN
	  };

	  //Create google map, place it in 'map-canvas' element, and use 'mapOptions' to 
	  //help configure it
	  map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

  	  //Add the "click" event listener to the map, so we can capture
  	  //lat lon from a google map click.
	  google.maps.event.addListener(map, "click", function(event) {
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();

		console.log("Volcanoes: " + $('#volcanoes').is(':checked'));
		console.log("Earthquakes: " + $('#earthquakes').is(':checked'));
		
		var echecked = $('#earthquakes').is(':checked');
		var vchecked = $('#volcanoes').is(':checked');

		var PostData = {
			lat:lat, 
			lng:lng , 
			earthQuakes: echecked , 
			volcanoes:vchecked
		}
		
		$.post( "backend.php", PostData)
		  .done(function( data ) {
			data = JSON.parse(data);
			console.log(data.Poly.length);
			deleteMarkers();
    		addPolygon(data.Poly);
		  });
	  });  
	  
	}	
	
	function addPolygon(obj) {
		var PolyCoords = [];
		

		for(i=0;i<obj.length;i++){
			var latlng = new google.maps.LatLng(obj[i][1],obj[i][0]);
			PolyCoords.push(latlng);
		}

		
		var polygon = new google.maps.Polygon({
			paths: PolyCoords,
			title: obj.fullname,
			fillColor: obj.Color,
			fillOpacity: 0.2,
			strokeWeight: 2,
			strokeColor: obj.Color,
			map: map
		});
		
		polygons.push(polygon); //Add polygon to global array of polygons
	}
	
	function addMarker(obj) {
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(obj.latitude,obj.longitude),
			title: obj.fullname,
			map: map
		});
		markers.push(marker); //Add marker to global array of markers
	}

	//Sets the map on all markers (could change the map, or set to null to erase markers)
	function setAllMap(map) {
		for (var i = 0; i < markers.length; i++) {
			markers[i].setMap(map);
			polygons[i].setMap(map);
		}
	}

	// Removes the markers from the map, but keeps them in the array.
	function clearMarkers() {
		setAllMap(null);
	}

	// Shows any markers currently in the array.
	function showMarkers() {
		setAllMap(map);
	}

	// Deletes all markers in the array by removing references to them.
	function deleteMarkers() {
		clearMarkers();
		markers = [];	//set global marker array to EMPTY
		polygons = [];
	}

	//Add a listener that runs "initialize" when page is done loading.
	google.maps.event.addDomListener(window, 'load', initialize);
	

    </script>
  </head>
  <body>
 <section>
    <div id="map-canvas"></div>
	<div id="form-stuff">
		Earthquakes: <input type="checkbox" id="earthquakes" value="true" checked><br>
		Volcanoes:<input type="checkbox" id="volcanoes" value="true"><br>
	</div>
</section>
  </body>
</html>
