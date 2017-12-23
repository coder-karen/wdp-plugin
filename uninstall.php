<?php
/**
 * Fired when the plugin is uninstalled.
 */
 

/* If uninstall not called from WordPress, then exit. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	wp_die( sprintf( __( '%s should only be called when uninstalling the plugin.', 'wdp-plugin' ), '<code>' . __FILE__ . '</code>' ) );
}

/* Deleting portfolio items */
function delete_posts() {
   
    global $wpdb;
    require plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-cpt.php';

    //Get the custom post-type slug in order to delete it
    $posttype = new WDP_Plugin_CPT();
    $posttype = $posttype->get_posttype();

    $posts = get_posts( array(
        'numberposts' => -1,
        'post_type' => $posttype,
        'post_status' => 'any' ) );

    foreach ( $posts as $post ){
        wp_delete_post( $post->ID, true );
    }

}

delete_posts();

/* Deleting options */
 
delete_option('wdp_settings');
delete_option('wdp-button-text');
delete_option('wdp-slug');
delete_option('wdp-previous-slug');

