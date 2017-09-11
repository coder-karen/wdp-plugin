<?php 
/*
Plugin Name: Web Developer's Portfolio Plugin
Plugin URI: http://karenattfield.com/wdp-plugin/
Description: A plugin that displays items within a portfolio on a designated portfolio page using custom post types via shortcodes. Designed to showcase screenshots from both desktop and mobile devices for each portfolio listing.
Author: Karen Attfield
Text Domain: wdp-plugin
Version: 1.0.1
Author URI: http://karenattfield.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
 */


/* exit if accessed directly */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/* Define variable for path to this plugin file. */
define( 'WDP_Plugin_Location', dirname( __FILE__ ) );

/* Code to run during plugin activation */
function activate_wdp_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-activator.php';
    WDP_Plugin_Activator::activate();
}

/* Code to run during plugin deactivation */
function deactivate_wdp_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-deactivator.php';
    WDP_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wdp_plugin');
register_deactivation_hook( __FILE__, 'deactivate_wdp_plugin');

/**
 * The core plugin files that are used to define 
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-public.php';
require plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-cpt.php';

/* Require the following files only if on the admin screen */
if ( is_admin() ) {
    require plugin_dir_path( __FILE__ ) . 'inc/class-wdp-plugin-admin.php';
}

/* Enqueues the styles for the plugin */
function wdp_styles() { 
    wp_enqueue_style( 'style',  plugins_url( '/css/style.css', __FILE__ ));
}

add_action('wp_enqueue_scripts', 'wdp_styles');

/* Enqueues the scripts for the plugin if on portfolio admin page */
function wdp_admin_scripts( $hook ){
    $cpt = 'portfolio';

    if( in_array($hook, array('post.php', 'post-new.php') ) ){
        $this_screen = get_current_screen();

        if( is_object( $this_screen ) && $cpt == $this_screen->post_type ){

            wp_enqueue_script( 'media-image-uploader', plugin_dir_url( __FILE__ ) . '/js/media-image-uploader.js', array( 'jquery' ), '1.0.0', true);
            wp_enqueue_script( 'delete-meta-ajax', plugin_dir_url( __FILE__ ) . '/js/delete-meta-ajax.js', array( 'jquery' ), '1.0.0', true ); 
        
            /* wp_localize_script() is used to pass values into JavaScript object properties to our js file */
            $media_script_values = array(
                'meta_image_title' => __('Select Mobile Image', 'wdp-plugin'),
                'button_title' => __('Set Mobile Image', 'wdp-plugin')
                );
            wp_localize_script('media-image-uploader', 'wdp_script_vars', $media_script_values);
   
        } // end if is object
    } // end if in array
}

add_action( 'admin_enqueue_scripts', 'wdp_admin_scripts' );


/* Load plugin textdomain for internationalization. */
function wdp_load_textdomain() {
  load_plugin_textdomain( 'wdp-plugin', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

add_action( 'init', 'wdp_load_textdomain' );