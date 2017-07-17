<?php

/* Maps meta field init ***************************************************************/

add_action("admin_init", "maps_meta_init");

function maps_meta_init(){
	add_meta_box("maps_meta_add_group", __("Add Group", "mapsian"), "maps_meta_add_group", "maps", "normal", "low");
	add_meta_box("maps_meta_add_current_group_list", __("Added group list", "mapsian"), "maps_meta_added_group_list", "maps", "normal", "low");
	add_meta_box("maps_meta_general_setting", __("General Settings", "mapsian"), "maps_meta_general_setting", "maps", "side", "low");
	add_meta_box("maps_meta_list_setting", __("List", "mapsian"), "maps_meta_list_setting", "maps", "side", "low");
}

function maps_meta_general_setting(){
	global $post;
	$mapsize = unserialize(get_post_meta($post->ID, 'map_size', true));
?>

<div>
	<div style="position:relative; top:5px; left:0px; font-size:14px; font-weight:bold">
		<?php _e('Map size', 'mapsian');?><br>
		<span style="font-weight:normal; font-style:italic"><?php _e("Please insert measure unit.ex)'px','%','em' (Default : pixel)", 'mapsian');?></span>
	</div>
	<div class="mapsize_pannel">
		<ul>
			<li class="mapsize_pannel_title"><?php _e('Width', 'mapsian');?> :</li>
			<li><input type="text" style="width:140px;" name="mapsize_width" value="<?php echo $mapsize[0];?>"></li>
		</ul>
		<ul>
			<li class="mapsize_pannel_title"><?php _e('Height', 'mapsian');?> :</li>
			<li><input type="text" style="width:140px" name="mapsize_height" value="<?php echo $mapsize[1];?>"></li>
		</ul>
	</div>
	<div style="position:relative; top:5px; left:0px; font-size:14px; font-weight:bold">
		<?php _e('UI setting', 'mapsian');?>
	</div>
	<div class="ui-setting">
		<ul>
			<li class="mapsize_pannel_title"><?php _e('Style', 'mapsian');?> :</li>
			<li>
				<select name="mapsian_ui">
					<option value="1">Default</option>
					<option value="1" <?php if($mapsize[2] == 1){ echo "selected"; }?>>Bottom Fixed</option>
					<option value="2" <?php if($mapsize[2] == 2){ echo "selected"; }?>>Right Fixed</option>
				</select>
			</li>
		</ul>
	</div>
</div>


<?php

}
// function maps_meta_add_group
function maps_meta_add_group(){
	global $post;
?>

<?php
	
	$groups = get_terms( 'mapsian_group',array('hide_empty' => 0) );

	if(!empty( $groups ) && !is_wp_error( $groups )){
		foreach($groups as $group){
			$group_value .= "<option value='".$group->term_id."'>".$group->name."</option>";
		}
	}
?>
<input type="hidden" id="post_id" value="<?php echo $post->ID;?>">
<div class="mapsian-meta-add-group-div">
	<ul class="mapsian-meta-add-group">
		<li><select id="selected_group"><option><?php _e('Select Group', 'mapsian');?></option><?php echo $group_value;?></select></li>
		<li><input type="button" class="button" value="<?php _e('Add', 'mapsian');?>" onclick="mapsian_add_group_to_map('<?php echo PLUGINSURL;?>',selected_group.value,<?php echo $post->ID;?>);"></li>
	</ul>
</div>

<?php

}
// Added group list function
function maps_meta_added_group_list(){

?>
<div style="width:90%" class="added_group_list_div">
<?php	
	global $post;

	$added_groups = get_post_meta($post->ID,"mapsian_added_groups", true);

	if($added_groups){

		$groups = explode(",",$added_groups);

		for ($i=0; $i < count($groups); $i++) {

		$terms_id = explode("_",$groups[$i]);
		$term_id = $terms_id[1];

		$term = get_term($term_id, "mapsian_group");

?>
	<ul class="mapsian-meta-added-group" id="mapsian_meta_added_group_<?php echo $term_id;?>">
		<li class="mapsian-meta-added-group-title"><?php echo $term->name;?></li>
		<li class="mapsian-meta-added-group-remove"><img src="<?php echo PLUGINSURL;?>/images/mapsian_icon_remove3.png" style="width:30px; height:auto; position:relative; top:-5px; cursor:pointer; cursor:hand" onclick="remove_added_group_by_maps('<?php echo PLUGINSURL;?>',<?php echo $term_id;?>,<?php echo $post->ID;?>);"></li>
		<li class="mapsian-meta-added-group-count-location"><?php echo _e('Added Locations', 'mapsian');?> : <b><?php echo number_format($term->count);?></b></li>
	</ul>

<?php
		}
	}
	else {
		//echo "Group is not added on this map. please add group first.";
	}
?>




</div>

<?php

}

function maps_meta_list_setting(){

	global $post;
	$list_options = get_post_meta($post->ID, "list_options", true );


	$list_options = unserialize($list_options);

	switch($list_options['display_list']){
		case "0" :
			$only_selected = "selected";
			$activation_tr = "deactive";
		break;

		case "1" :
			$maplist_selected = "selected";
			$activation_tr = "";
		break;

		default :
			$activation_tr = "deactive";
		break;
	}

	switch($list_options['list_layout']){
		case "Grid" :
			$grid_selected = "selected";
		break;

		case "Column" :
			$column_selected = "selected";
		break;
	}

	switch($list_options['shop_of_column']){
		case "1" :
			$one_selected = "selected";
		break;

		case "2" :
			$two_selected = "selected";
		break;

		case "3" :
			$three_selected = "selected";
		break;

		case "4" :
			$four_selected = "selected";
		break;

		case "5" :
			$five_selected = "selected";
		break;
	}

	switch($list_options['thumbnails']){
		case "Display" :
			$display_selected = "selected";
		break;

		case "Hide" :
			$hide_selected = "selected";
		break;
	}


?>

	<table>
		<tr class="maps_metabox_display_list">
			<th>Display list?</th>
			<td>
				<select name="select_display_list">
					<option value="0" <?php echo $only_selected;?>>Map only</option>
					<option value="1" <?php echo $maplist_selected;?>>Map + List</option>
				</select>
			</td>
		</tr>
		<tr class="maps_metabox_list_layout <?php echo $activation_tr;?>">
			<th>List layout</th>
			<td>
				<select name="list_layout">
					<option <?php echo $grid_selected;?>>Grid</option>
					<option <?php echo $column_selected;?>>Column</option>
				</select>
			</td>
		</tr>
		<tr class="maps_metabox_column_count <?php echo $activation_tr;?>">
			<th># of Column</th>
			<td>
				<select name="shop_of_column">
					<option <?php echo $one_selected;?>>1</option>
					<option <?php echo $two_selected;?>>2</option>
					<option <?php echo $three_selected;?>>3</option>
					<option <?php echo $four_selected;?>>4</option>
					<option <?php echo $five_selected;?>>5</option>
				</select>
			</td>
		</tr>
		<tr class="maps_metabox_thumbnails">
			<th>Thumbnails <?php echo $social_share;?></th>
			<td>
				<select name="thumbnails">
					<option <?php echo $display_selected;?>>Display</option>
					<option <?php echo $hide_selected;?>>Hide</option>
				</select>
			</td>
		</tr>
	</table>

<?php
}
?>