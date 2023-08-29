<?php
/*
Plugin Name: Unclickable Menu Items
Description: Adds the ability to make a WordPress menu item unclickable
Version: 1.0
Author: Chris 'Xenon' Hanson
*/

// This output can only be seen by watching the server's logs (i.e., ssh'ing into the
// WordPress VPS and 'tail -f' the site's specific error output file.
function error_log_wrap($name, $var) {
	$msg = print_r($var, true);

	error_log(">>> START {$name}\n");
	error_log(">>> '{$msg}'\n");
	error_log(">>> STOP {$name}\n");
}

wp_enqueue_style("unclickable_menu_style", "unlickable-menu.css");

add_action("wp_nav_menu_item_custom_fields", "unclickable_menu_add_checkbox", 10, 4);
function unclickable_menu_add_checkbox($item_id, $item, $depth, $args) {
	// TODO: I'm not entirely sure why/what this does?
	// $ckt = esc_html_e("Unclickable", "unclickable-menu-items");
	$ck_gpm = get_post_meta($item_id, "_menu_item_unclickable", true);
	$ck = checked($ck_gpm, true, false);

	// error_log_wrap("item", $item);
	// error_log_wrap("ck", $ck);
	// error_log_wrap("ckt", $ckt);
	// error_log_wrap("ck_gpm", $ck_gpm);

	echo "<p class='field-unclickable description description-wide'>
		<input type='checkbox' id='edit-menu-item-unclickable-{$item_id}'
			class='widefat code edit-menu-item-unclickable'
			name='menu-item-unclickable[{$item_id}]'
			value='1' {$ck}
		/>
		<label for='edit-menu-item-unclickable-{$item_id}'>Unclikable</label>
	</p>";
	// TODO: In the <label> above, INSTEAD of hardcoding "Unclickable", the original
	// code instead inject the value of the (commented-out) $ckt variable above. But, why?
}

add_action("wp_update_nav_menu_item", "unclickable_menu_item_update", 10, 3 );
function unclickable_menu_item_update($menu_id, $menu_item_db_id, $args) {
	if(!isset($_REQUEST["menu-item-unclickable"][$menu_item_db_id])) {
		$unclickable_value = "";
	}

	else {
		$unclickable_value = sanitize_text_field($_REQUEST["menu-item-unclickable"][$menu_item_db_id]);
	}

	// error_log_wrap("unclickable_value(action)", $unclickable_value);

	update_post_meta($menu_item_db_id, "_menu_item_unclickable", $unclickable_value);
}

add_filter("nav_menu_link_attributes", "unclickable_menu_item_link_attributes", 10, 3);
function unclickable_menu_item_link_attributes($atts, $item, $args) {
	$unclickable_value = get_post_meta($item->ID, "_menu_item_unclickable", true);

	if(!empty($unclickable_value)) {
		$atts["href"] = "#";
		$atts["class"] .= " unclickable";
	}

	// error_log_wrap("unclickable_value(filter)", $unclickable_value);
	// error_log_wrap("atts", $atts);

	return $atts;
}
