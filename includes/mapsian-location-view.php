<?php

add_shortcode("location-view","location_view");

function location_view($attr, $content = null ){

$location_id = $attr['id'];

$locations = explode(",",$location_id);

$content = "<div id='mapsian_outgrid'><div id='mapsian_maps_".$location_id."' class='mapsian_maps' style='width:100%; height:400px'></div></div>";

$content = wpautop(trim($content));

$content .= 

"<script>
	initialize(0,0,null,'".PLUGINSURL."',null,null,".$location_id.");
	var latlngbounds = new google.maps.LatLngBounds(); 
	var latlng_pos;";

for ($i=0; $i < count($locations); $i++) { 

	//this will check whether data is base64 encoded or not
	$get_location_detail = get_post_meta($locations[$i], 'location_detail', true );

	if( unserialize(base64_decode($get_location_detail, true)) == true ){
		$location_meta = unserialize(base64_decode($get_location_detail));
	} else {
		$location_meta = unserialize($get_location_detail);
	}


	$title 		= get_the_title($locations[$i]);
	$desc 		= $location_meta[0];
	$url 		= $location_meta[1];
	$address 	= $location_meta[2];
	$lat 		= $location_meta[3];
	$lng 		= $location_meta[4];

	$content .= 
	"
	setMarker(".$lat.",".$lng.",'".$title."','".$desc."','".$url."',".$location_id.");
	latlng_pos = new google.maps.LatLng(".$lat.",".$lng.");
	latlngbounds.extend(latlng_pos);";
}

	$content .= "globalMap[".$location_id."].fitBounds(latlngbounds);</script>";

return $content;

}
?>