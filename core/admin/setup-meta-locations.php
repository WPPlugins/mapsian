<?php

/* Maps meta field init ***************************************************************/

add_action("admin_init", "locations_meta_init");

function locations_meta_init(){
	add_meta_box("locations_meta_description", __("Description", "mapsian"), "locations_meta_description", "locations", "normal", "low");
	add_meta_box("locations_meta_linkurl", __("Basic Information", "mapsian"), "locations_meta_linkurl", "locations", "normal", "low");
	add_meta_box("locations_meta_preview_map", __("Preview google map", "mapsian"), "locations_meta_preview_map", "locations", "normal", "low");
	register_taxonomy_for_object_type('mapsian_group', 'locations'); 
}

function locations_meta_description(){ 
	global $post;
	//$location_meta = unserialize(base64_decode(get_post_meta($post->ID, 'location_detail', true )));

	//this will check whether data is base64 encoded or not
	//$get_location_detail = unserialize(get_post_meta($post->ID, 'location_detail', true ));

	//this will check whether data is base64 encoded or not
	$get_location_detail = get_post_meta($post->ID, 'location_detail', true );

	if( unserialize(base64_decode($get_location_detail, true)) == true ){
		$location_meta = unserialize(base64_decode($get_location_detail));
	} else {
		$location_meta = unserialize($get_location_detail);
	}

  $location_meta_sanitized = wp_kses(
    $location_meta[0],
    array(
      'p'       => array(),
      'br'      => array(),
      'font'    => array(),
      'strong'  => array(),
      'b'       => array(),
      'i'       => array(),
      'strike'  => array(),
      'h1'      => array(),
      'h2'      => array(),
      'h3'      => array(),
      'h4'      => array(),
      'h5'      => array(),
    )
  );

?>

<div>
	<textarea name="mapsian_location_description" class="mapsian_location_description"><?php echo $location_meta_sanitized;?></textarea>
</div>

<?php

}

function locations_meta_linkurl(){
	global $post;

	//this will check whether data is base64 encoded or not
	$get_location_detail = get_post_meta($post->ID, 'location_detail', true );

	if( unserialize(base64_decode($get_location_detail, true)) == true ){
		$location_meta = unserialize(base64_decode($get_location_detail));
	} else {
		$location_meta = unserialize($get_location_detail);
	}

	//$location_meta = unserialize(base64_decode(get_post_meta($post->ID, 'location_detail', true )));

	switch($location_meta[6]){
		case "same" :
			$same_checked = "checked";
		break;

		case "new" :
			$new_checked = "checked";
		break;

		default :
			$same_checked = "checked";
		break;
	}
?>

<table style="width:100%">
	<tr style="height:40px; vertical-align:top">
		<th>Address</th>
		<td colspan="4"><input type="text" id='full_address' name='full_address' value="<?php echo stripslashes($location_meta[2]);?>" onchange="check_geo_maps_address();"> <input type="button" class="button" value="Get it" onclick="check_geo_maps_address()"></td>
	</tr>
	<tr style="height:50px; vertical-align:top">
		<th></th>
		<td style="Width:80px;padding-top:5px"><?php echo _e('Latitude', 'mapsian');?> : </td>
		<td style="width:180px; text-align:left; padding-right:30px"><input type="text" class="mapsian_lat" name="mapsian_lat" value="<?php echo $location_meta[3];?>"></td>
		<td style="width:80px;padding-top:5px"><?php echo _e('Longitude', 'mapsian');?> : </td>
		<td><input type="text" class="mapsian_lng" name="mapsian_lng" value="<?php echo $location_meta[4];?>"></td>		
	</tr>
	<tr>
		<th>Link</th>
		<td colspan="4"><input type="text" name="mapsian_location_linkurl" class="mapsian_location_linkurl" value="<?php echo $location_meta[1];?>"></td>
	</tr>
	<tr>
		<th></th>
		<td colspan="4"><input type="radio" name="open_window" value="same" <?php echo $same_checked;?>> Open same tab/window &nbsp;&nbsp;<input type="radio" name="open_window" value="new" <?php echo $new_checked;?>> Open new window</td>
	</tr>
</table>

<?php

}

function locations_meta_preview_map(){
	global $post;
	//this will check whether data is base64 encoded or not
	$get_location_detail = get_post_meta($post->ID, 'location_detail', true );

	if( unserialize(base64_decode($get_location_detail, true)) == true ){
		$location_meta = unserialize(base64_decode($get_location_detail));
	} else {
		$location_meta = unserialize($get_location_detail);
	}
	if($location_meta[3] and $location_meta[4]){
?>
	<script>
	jQuery(document).ready(function(){
		add_location_single_step(<?php echo $location_meta[3];?>,<?php echo $location_meta[4];?>);
	});
	</script>
<?php
	}
?>
<div style="height:400px" class="admin_modify_marker_map">
Google map will be appear after searching address. please do search address first.
</div>
<?php 

}

?>