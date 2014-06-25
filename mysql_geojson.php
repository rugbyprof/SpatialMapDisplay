<?php
/**
 * Title:   MySQL to GeoJSON (Requires https://github.com/phayes/geoPHP)
 * Notes:   Query a MySQL table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */

# Include required geoPHP library and define wkb_to_json function
include_once('geoPHP/geoPHP.inc');
function wkb_to_json($wkb) {
    $geom = geoPHP::load($wkb,'wkb');
    return $geom->out('json');
}

if(isset($argv[1]) && $argv[1]=='debug' || isset($_GET['debug']) && $_GET['debug']){
	$_POST['lat'] = 33.546;
	$_POST['lng'] = -122.546;
	$debug = true;
}

# Connect to MySQL database
$conn = new PDO('mysql:host=localhost;dbname=5443_SpatialData','5443','5443');

# Build SQL SELECT statement and return the geometry as a WKB element


$sql1 = "
	SELECT 
		OGR_FID,
		fullname, 
		latitude, 
		longitude,
		NumGeometries(SHAPE) AS Multi,
		AsWKB(SHAPE) as wkb, 
		69*haversine(latitude,longitude,latpoint, longpoint) AS distance_in_miles
	FROM military_installations
	JOIN (
	SELECT  {$_POST['lat']}  AS latpoint,  {$_POST['lng']} AS longpoint
	) AS p
	ORDER BY distance_in_miles
	LIMIT 10
";

$sql2 = "
SELECT year,month,day,location, AsWKB(SHAPE) AS wkb 
FROM earth_quakes
LIMIT 10
";

$sql = $sql1;

# Try query or error
$rs = $conn->query($sql);
if (!$rs) {
    echo 'An SQL error occured.\n';
    print_r($sql);
    exit;
}

# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove wkb and geometry fields from properties
    unset($properties['wkb']);
    unset($properties['SHAPE']);
    $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode(wkb_to_json($row['wkb'])),
         'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
?>
