<?php

defined(('WP_UNINSTALL_PLUGIN') or die());

// delete post type from db
//global $wpdb;
//$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type IN ('room')");
// delete post meta from db
// delete comments from db

// delete post type from db without access to DB
$rooms = get_posts(array('post_type' => 'room', 'numberposts' => -1));
foreach ($rooms as $room) {
    wp_delete_post($room->ID, true);
}
