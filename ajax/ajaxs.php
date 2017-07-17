<?php

add_action( 'wp_ajax_maps_status_change', 'maps_status_change_callback' );

function maps_status_change_callback() {

global $wpdb;

$map_id = sanitize_text_field( $_POST['map_id'] );
$post = get_post($map_id);

$old_status = $post->post_status;

if($old_status == "publish"){

$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $post->ID ) );
clean_post_cache( $post->ID );
$post->post_status = 'draft';
wp_transition_post_status( 'draft', $old_status, $post);

$result = "draft";

}
else {

$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post->ID ) );
clean_post_cache( $post->ID );
$post->post_status = 'publish';
wp_transition_post_status( 'publish', $old_status, $post);

$result = "publish";

}

echo $result;

	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_add_group_to_map', 'add_group_to_map_callback' );

function add_group_to_map_callback(){

global $wpdb;

$term_id = sanitize_text_field( $_POST['term_id'] );
$post_id = sanitize_text_field( $_POST['post_id'] );
$find_id = "group_".$term_id;

if($term_id and $post_id){

	$term = get_term($term_id, "mapsian_group");

	$added_groups = get_post_meta($post_id, "mapsian_added_groups", true);

	if($added_groups){

		if(strpos($added_groups, $find_id) !== false){
			return false;
		}
		else {
			update_post_meta( $post_id, "mapsian_added_groups", $added_groups.",".$find_id );
?>
	<ul class="mapsian-meta-added-group" id="mapsian_meta_added_group_<?php echo $term_id;?>">
		<li class="mapsian-meta-added-group-title"><?php echo $term->name;?></li>
		<li class="mapsian-meta-added-group-remove"><img src="<?php echo PLUGINSURL;?>/images/mapsian_icon_remove3.png" style="width:30px; height:auto; position:relative; top:-5px; cursor:pointer; cursor:hand" onclick="remove_added_group_by_maps('<?php echo PLUGINSURL;?>',<?php echo $term_id;?>,<?php echo $post_id;?>);"></li>
		<li class="mapsian-meta-added-group-count-location">Added Locations : <b><?php echo number_format($term->count);?></b></li>
	</ul>
<?php
		}
	}
	else {
		update_post_meta( $post_id, "mapsian_added_groups", $find_id );
?>
	<ul class="mapsian-meta-added-group" id="mapsian_meta_added_group_<?php echo $term_id;?>">
		<li class="mapsian-meta-added-group-title"><?php echo $term->name;?></li>
		<li class="mapsian-meta-added-group-remove"><img src="<?php echo PLUGINSURL;?>/images/mapsian_icon_remove3.png" style="width:30px; height:auto; position:relative; top:-5px; cursor:pointer; cursor:hand" onclick="remove_added_group_by_maps('<?php echo PLUGINSURL;?>',<?php echo $term_id;?>,<?php echo $post_id;?>);"></li>
		<li class="mapsian-meta-added-group-count-location">Added Locations : <b><?php echo number_format($term->count);?></b></li>
	</ul>
<?php
	}

}

die();

}

add_action( 'wp_ajax_remove_added_group_by_maps', 'remove_added_group_by_maps_callback' );

function remove_added_group_by_maps_callback(){


global $wpdb;

$term_id = sanitize_text_field( $_POST['term_id'] );
$post_id = sanitize_text_field( $_POST['post_id'] );
$find_id = "group_".$term_id;

if($term_id and $post_id){

	$term = get_term($term_id, "mapsian_group");
	$added_groups = get_post_meta($post_id, "mapsian_added_groups", true);

	if($added_groups){
		$added_groups = str_replace($find_id.",", "", $added_groups);
		$added_groups = str_replace(",".$find_id,"",$added_groups);
		$added_groups = str_replace($find_id, "", $added_groups);

		update_post_meta( $post_id, "mapsian_added_groups", $added_groups );
	}
}
else {
	return false;
}

die();

}


add_action( 'wp_ajax_getjson_data', 'getjson_data' );
add_action( 'wp_ajax_nopriv_getjson_data', 'getjson_data' );


function getjson_data(){
 
$map_id 	= sanitize_text_field( $_POST['map_id'] );
$term_id 	= sanitize_text_field( $_POST['group_id'] );
$keyword 	= sanitize_text_field( $_POST['keyword'] );
$location_id		= sanitize_text_field( $_POST['location_id'] );

if($term_id){

	$group_term = get_term($term_id,"mapsian_group");
	$term_slug = $group_term->slug;

	$args = array(
		'post_type' => 'locations',
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'mapsian_group',
				'field' => 'slug',
				'terms' => $term_slug
				)
		),
		'posts_per_page' => -1
	); 

}

elseif($term_id == 0 and $keyword != "" ){

$added_groups = get_post_meta($map_id,"mapsian_added_groups",true);
$array_groups = explode(",",$added_groups);

for ($i=0; $i < count($array_groups); $i++) { 
	$group_id = str_replace("group_","",$array_groups[$i]);
	$terms = get_term($group_id,"mapsian_group");
	$array_terms[$i] = $terms->slug;
}

	$args = array(
		'post_type' => 'locations',
		'post_status' => 'publish',
		's'			=> $keyword,
		'tax_query' => array(
			array(
				'taxonomy' => 'mapsian_group',
				'field' => 'slug',
				'terms' => $array_terms
				)
		)		
	);	



}

elseif($term_id == 0 and $keyword == "" and $location_id != ""){

$added_groups = get_post_meta($map_id,"mapsian_added_groups",true);
$array_groups = explode(",",$added_groups);

for ($i=0; $i < count($array_groups); $i++) { 
	$group_id = str_replace("group_","",$array_groups[$i]);
	$terms = get_term($group_id,"mapsian_group");
	$array_terms[$i] = $terms->slug;
}

	$args = array(
		'post_type' => 'locations',
		'post_status' => 'publish',
		'ID'		=> $location_id,
		'posts_per_page' => -1
	);

}

else {

$added_groups = get_post_meta($map_id,"mapsian_added_groups",true);
$array_groups = explode(",",$added_groups);

for ($i=0; $i < count($array_groups); $i++) { 
	$group_id = str_replace("group_","",$array_groups[$i]);
	$terms = get_term($group_id,"mapsian_group");
	$array_terms[$i] = $terms->slug;
}

	$args = array(
		'post_type' => 'locations',
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'mapsian_group',
				'field' => 'slug',
				'terms' => $array_terms
				)
		),
		'posts_per_page' => -1
	);
}

$locations = get_posts($args);
$Cnt = 0;
if(count($locations) > 0){
foreach ($locations as $location) {

	$post_thumbnail_id = get_post_thumbnail_id( $location->ID );
	$full_image = wp_get_attachment_image_src($post_thumbnail_id, array(170,170));
	$thumbnail = wp_get_attachment_thumb_url($post_thumbnail_id);

	//this will check whether data is base64 encoded or not
	$get_location_detail = get_post_meta($location->ID, 'location_detail', true);


	if( unserialize(base64_decode($get_location_detail, true)) == true ){
		$location_meta = unserialize(base64_decode($get_location_detail));
	} else {
		$location_meta = unserialize($get_location_detail);
	}

	$resultArray[$Cnt] = array();
	$resultArray[$Cnt]['title']			 = get_the_title($location->ID);
	$resultArray[$Cnt]['desc']			 = $location_meta[0];
	$resultArray[$Cnt]['url']			 = $location_meta[1];
	$resultArray[$Cnt]['addr']			 = $location_meta[2];  
	$resultArray[$Cnt]['lat']            = $location_meta[3];
	$resultArray[$Cnt]['lng']            = $location_meta[4];
	$resultArray[$Cnt]['thumb']			 = $thumbnail;
	$resultArray[$Cnt]['loc_id']		 = $location->ID;
	$resultArray[$Cnt]['full_image']	 = $full_image[0];
	$resultArray[$Cnt]['full_width']	 = $full_image[1];
	$resultArray[$Cnt]['full_height']	 = $full_image[2];
	$resultArray[$Cnt]['open_window']	 = $location_meta[6];
	
	$Cnt++;
}
	header("Content-type: application/json");
	echo json_encode($resultArray);  

}
else {
	return false;


}

die();


}

?>