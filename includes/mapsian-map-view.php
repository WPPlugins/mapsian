<?php

add_shortcode("map-view","map_view");

function map_view($attr){

$map_id = $attr['id'];

$groups = get_post_meta($map_id,"mapsian_added_groups",true);
$groups = explode(",",$groups);

$list_options = get_post_meta($map_id, "list_options", true );
$list_options = unserialize($list_options);

$display 		= $list_options['display_list'];
$list_layout 	= $list_options['list_layout'];
$shop_of_column = $list_options['shop_of_column'];
$thumbnails		= $list_options['thumbnails'];


$mapsize = unserialize(get_post_meta($map_id, 'map_size', true));


if($mapsize[0] == "" or $mapsize[1] == ""){
	$mapsize[0] = "100%";
	$mapsize[1] = "400px";
}

if(strpos($mapsize[0], "%") === false and strpos($mapsize[0], "px") === false){
	$mapsize[0] = $mapsize[0]."px";
}
if(strpos($mapsize[1], "%") === false and strpos($mapsize[1], "px") === false){
	$mapsize[1] = $mapsize[1]."px";
}

$real_height = str_replace("%","",$mapsize[1]);
$real_height = str_replace("px","",$mapsize[1]);
$real_height = str_replace("em","",$mapsize[1]);

for ($i=0; $i < count($groups); $i++) { 
	$group_id = str_replace("group_", "", $groups[$i]);
	$group_names = get_term($group_id,"mapsian_group");

	$group_name[$i] = $group_names->name;
	$group_term_id[$i] = $group_names->term_id;
	$group_slug[$i] = $group_names->slug;
	$group_count[$i] = $group_names->count;
	$total_count += $group_names->count;
}
		$group_button .= "<li class='each_group_menu' onclick=\"switch_group(".$map_id.",'','".PLUGINSURL."','','".$display."','".$list_layout."','".$shop_of_column."','".$thumbnails."');\">View All</li>";
		$group_button2 .= "<li onclick=\"switch_group(".$map_id.",'','".PLUGINSURL."','','".$display."','".$list_layout."','".$shop_of_column."','".$thumbnails."');\">View All</li>";

	for ($i=0; $i < count($group_name); $i++) { 
		$group_button .= "<li class='each_group_menu' id='each_group_menu_".$group_term_id[$i]."' onclick=\"switch_group(".$map_id.",".$group_term_id[$i].",'".PLUGINSURL."','".$group_name[$i]."','".$display."','".$list_layout."','".$shop_of_column."','".$thumbnails."');\">".$group_name[$i]."<div class='location_count'><img src='".PLUGINSURL."/images/mapsian_icon_balloon.png' style='width:40px; height:auto'><div class='location_count_number'>".$group_count[$i]."</div></div></li>";

		$group_button2 .= "<li id='each_group_menu_".$group_term_id[$i]."' onclick=\"switch_group(".$map_id.",".$group_term_id[$i].",'".PLUGINSURL."','".$group_name[$i]."','".$display."','".$list_layout."','".$shop_of_column."','".$thumbnails."');\">".$group_name[$i]."</li>";
	}


?>

<?php

	$contents = '
	<div id="mapsian_outgrid">
	<input type="hidden" class="thumb_status" value="'.$thumbnails.'">
	<input type="hidden" class="layout" value="'.$list_layout.'">
	<input type="hidden" class="pannel_status">
	<div class="mapsian_map_view_area">
		<ul>
			<li style="margin:0">
				<div id="mapsian_maps_'.$map_id.'" class="mapsian_maps" style="width:100%; height:'.$mapsize[1].'"></div>';

	if ($mapsize[2] == 1){


				$contents .='<div id="mapsian_maps_top_bg"></div>
				<div id="mapsian_maps_title">'.get_the_title($map_id).'</div>
				<div id="mapsian_maps_search">
					<ul>
						<li style="float:left"><input type="text" name="search_keyword" onchange="search_location('.$attr['id'].',\''.PLUGINSURL.'\');"></li>
						<li style="float:left"><img src="'.PLUGINSURL.'/images/mapsian_icon_search.png" style="width:30px; height:auto; position:relative; top:0px; left:5px; cursor:pointer; cursor:hand; margin-right:10px" onclick="search_location('.$attr['id'].');"></li>
					</ul>
				</div>
				<div id="mapsian_maps_uder_menu">
				</div>
				<div id="mapsian_maps_each_menu">
					<ul>
						'.$group_button2.'
					</ul>
				</div>';

}
			$contents .='</li>
			<li class="group_title_flow"></li>
			<li class="loading_flow_bg"><li>
			<li class="loading_flow_title">
				<ul>
					<li style="text-align:center"><img src="'.PLUGINSURL.'/images/mapsian_icon_loading2.gif"></li>
					<li style="font-size:16px; margin-top:10px;">'.__('Now loading', 'mapsian').'</li>
				</ul>
			</li>

			<li class="loading_flow_faild">'.__("No locations found.", 'mapsian').'</li>';


	if ($mapsize[2] == 2){

			$contents .= '<li class="group_select_pannel">
				<div class="group_select_arrow" onclick="pannel_flow();"><</div>
				<div class="group_menu_area" style="height:90%; position:relative; top:5%;">
				<div class="group_menu">
					<ul style="margin:0; padding:0">
						<li style="padding:0px 0px 10px 0px !important; margin:0;"><input type="text" style="width:60%" name="search_keyword" onchange="search_location('.$attr['id'].',\''.PLUGINSURL.'\');"><img src="'.PLUGINSURL.'/images/mapsian_icon_search.png" style="width:30px; height:auto; position:relative; top:10px; left:5px; cursor:pointer; cursor:hand" onclick="search_location('.$attr['id'].',\''.PLUGINSURL.'\');"></li>
					</ul>
					<ul>
						'.$group_button.'
					</ul>
				</div>
				</div>
			</li>';

	}
		$contents .= '</ul>
	</div>
	<div style="margin-top:10px" class="map_list">

	</div>
</div>
<script>
initialize(0,0,'.$attr['id'].',\''.PLUGINSURL.'\',null,null,'.$map_id.');
getJSON('.$attr['id'].',\''.PLUGINSURL.'\',0,\''.$display.'\', \''.$list_layout.'\', \''.$shop_of_column.'\', \''.$thumbnails.'\');
</script>';

$content = wpautop(trim($contents));

return $content;

}
