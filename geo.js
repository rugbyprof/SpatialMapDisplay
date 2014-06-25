var map;
var markers = [];
var polygons = [];
var temp = [];

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

        console.log("lat: " + lat);
        console.log("lng: " + lng);
        
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

        $.post("class.geojson.php", PostData)
            .done(function( data ) {
                //data = JSON.parse(data);
                var obj = data.features;
                for (var i in obj){
                    traverse([i,obj[i]]);
                }
                //console.log(data.Poly.length);
                //deleteMarkers();
                //addPolygon(data.Poly);
            });
    });  

}

//function traverse(jsonObj) {
//    if( typeof jsonObj == "object" ) {
//        if(jsonObj !== null){
//            $.each(jsonObj, function(k,v) {
//                // k is either an array index or object key
//                traverse(v);
//            });
//        }
//    }
//    else {
//        // jsonOb is a number or string
//        console.log(jsonObj);
//    }
//}

function process(key,value) {
    if(isInt(key) === true)
        console.log(key + " : "+value);
}

function traverse(o,func) {
    for (var i in o) {
        func.apply(this,[i,o[i]]);  
        if (o[i] !== null && typeof(o[i])=="object") {
            //going on step down in the object tree!!
            traverse(o[i],func);
        }
    }
}

function isInt(value) { 
    return !isNaN(parseInt(value,10)) && (parseFloat(value,10) == parseInt(value,10)); 
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

