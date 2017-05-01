<?php 
/**
 * Fired during plugin activation.
 */


class WDP_Plugin_Activator {

    /* Function to run during plugin activation */
    public static function activate() {

        require_once('class-wdp-plugin-cpt.php');

        if ( ! current_user_can( 'activate_plugins' ) )
            return;

        (new WDP_Plugin_CPT)->wdp_projects_register();

        flush_rewrite_rules();

    }

}


