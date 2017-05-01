<?php 
/**
 * Fired during plugin de-activation.
 */


class WDP_Plugin_Deactivator {

    /* Function to run during plugin activation */
    public static function deactivate() {
            
        require_once('class-wdp-plugin-cpt.php');
        
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        
        flush_rewrite_rules();

    }

}  
