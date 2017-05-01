<?php
/**
 * Fired when the plugin is uninstalled.
 */
 

/* If uninstall not called from WordPress, then exit. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	wp_die( sprintf( __( '%s should only be called when uninstalling the plugin.', 'wdp-plugin' ), '<code>' . __FILE__ . '</code>' ) );
}

/* Deleting options */

$option_name = 'wdp_settings';
 
delete_option($option_name);


/* Deleting portfolio items */
function delete_posts() {
   
    global $wpdb;

    $posts = get_posts( array(
        'numberposts' => -1,
        'post_type' => 'portfolio',
        'post_status' => 'any' ) );

    foreach ( $posts as $post ){
        wp_delete_post( $post->ID, true );
    }

}

delete_posts();
