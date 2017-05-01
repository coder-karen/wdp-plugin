<?php
/*
* Custom post type functionality of the plugin
* Registering post types and creating meta boxes
*/


if ( ! class_exists( 'WDP_Plugin_CPT' ) ) {

    /**
     * Main Web_Developers_Portfolio class
     */
    class WDP_Plugin_CPT {

			   /**
        * Initialize the class and set its properties.
        */
	    public function __construct(){

			add_action('init', array($this, 'wdp_projects_register'));
			add_action("add_meta_boxes", array($this, "wdp_projects_admin_init"));
			add_action( 'wp_ajax_wdp_delete_meta', array($this, 'wdp_delete_meta' ));
			add_action( 'save_post', array($this, 'mobile_image_save' ));
			add_action( 'save_post', array($this, 'my_url_save_metabox' ));
            add_action('do_meta_boxes', array($this,'replace_featured_image_box'));  

		}

        /* Register the portfolio post type */
        public function wdp_projects_register() {

            // Setting the option value on the settings page as false unless checked
          	$options = get_option( 'wdp_settings' );
          	$optionvalue = false;
          	if ($options != '') {
          		  $optionvalue = true;
          	} 
        		else { 
        			 $optionvalue = false;
        		} 
                   

            $labels = array(
                'name'                => __( 'Portfolio', 'wdp-plugin' ),
                'singular_name'       => __( 'Portfolio', 'wdp-plugin' ),
                'add_new'             => __( 'Add New', 'wdp-plugin' ),
                'add_new_item'        => __( 'Add New Portfolio Item', 'wdp-plugin' ),
                'edit_item'           => __( 'Edit Portfolio Item', 'wdp-plugin' ),
                'new_item'            => __( 'New Portfolio Item', 'wdp-plugin' ),
                'all_items'           => __( 'All Portfolio Items', 'wdp-plugin' ),
                'view_item'           => __( 'View Portfolio Item', 'wdp-plugin' ),
                'view_items'          => __( 'View Portfolio Items', 'wdp-plugin' ),
                'search_items'        => __( 'Search Portfolio', 'wdp-plugin' ),
                'attributes'          => __( 'Portfolio Item Order', 'wdp-plugin'),
                'not_found'           => __( 'No portfolio items found', 'wdp-plugin' ),
                'not_found_in_trash'  => __( 'No portfolio items found in Trash', 'wdp-plugin' ),
                'menu_name'           => __( 'Portfolio', 'wdp-plugin' ),

                );

            $args = array(
                'labels'              => $labels,
                'singular_label'      => __('Project', 'wdp-plugin'),
                'public'              => true,
                'publicly_queryable'  => $optionvalue,
                'show_ui'             => true,
                'capability_type'     => 'post',
                'query_var'           => true,
                'heirarchical'        => false,
                'has_archive'         => $optionvalue, 
                'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
                'rewrite'             => array('slug' => 'portfolio', 'with_front' => true),  
                'rewrite'             => true,
                'menu_position'       => 20
                );


        		register_post_type('portfolio', $args);
        		
        }


        /* Initializing the portfolio admin page meta boxes */
        public function wdp_projects_admin_init() {
          	add_meta_box("wdp-projects-meta", 
            		__("Project Link", "wdp-plugin"),
            		array($this,"wdp_projects_url"), 
            		"portfolio", 
            		"side", 
            		"low"
            		);

            
            add_meta_box( 'mobileimagediv', 
              	__( 'Mobile Image', 'wdp-plugin' ), 
              	array($this,'mobile_image_metabox'), 
              	'portfolio', 
              	'side', 
              	'low'
                );
             
        }

                
        /* Changing the name of the default 'featured image' box */        
        public function replace_featured_image_box() { 

            if (get_post_type() == 'portfolio') {
                remove_meta_box( 'postimagediv', 'portfolio', 'side' );  
                add_meta_box('postimagediv', esc_html__("Desktop Image", "wdp-plugin"), 'post_thumbnail_meta_box', 'portfolio', 'side', 'low');  
            }

        }  


        /* Helper function to get portfolio/post ID */
        private function get_my_ID() {

            $post = get_post();
            return ! empty( $post ) ? $post->ID : false;

         }

        /* Function to output the contents of the mobile-image metabox */
        public function mobile_image_metabox ( $post ) {

            global $post;  
        		$meta = (get_post_meta( $post->ID, '_mobile_image_id', false)); 
        		if (isset ($meta[0])) {
        		  $displayimage = $meta[0];
        	  }
            
            // Create a nonce field for submitting the image 
            wp_nonce_field( 'submit_image_nonce', 'submit_image' ); ?>

            <!-- Build the html fields within the mobile image metabox -->
            <p>
            <!-- If there is no image set, create an upload image label: -->
            <?php if (!isset ($displayimage)) { ?>
        	  <label for="_mobile_image_id"><?php esc_html_e("Upload image", "wdp-plugin")?></label>

            <!-- If there is an image set, create and hide an upload image label: -->
            <?php } else { ?>
            <label for="_mobile_image_id" style="display:none;"><?php esc_html_e("Image Upload", "wdp-plugin")?></label><br>
            <?php } ?>

            <!-- If there is no image set, create an input text field for an image url: -->
            <?php if (!isset ($displayimage)) { ?>
        	  <input type="text" name="_mobile_image_id" id="_mobile_image_id" class="meta-image regular-text" value="<?php if (isset ($displayimage)) {echo $displayimage; } else { echo ""; } ?>" style="max-width:250px; height: 28px; margin: 2px 4px 4px 0" >

            <!-- If there is an image set, create and hide an input text field for an image url: -->
          	<?php } else { ?>
          	<input type="text" name="_mobile_image_id" id="_mobile_image_id" class="meta-image regular-text" value="<?php if (isset ($displayimage)) {echo $displayimage; } else { echo ""; } ?>" style="max-width: 250px; height: 28px; margin: 2px 4px 4px 0" display:none;">

            <!-- If there is no image set, create an upload button -->
            <?php } ?>
            <?php if (!isset ($displayimage)) { ?>
        	  <input type="button" id="image-upload" class="button image-upload" value=<?php esc_html_e("Browse", "wdp-plugin"); ?> >
         
            <!-- If there is an image set, create and hide an upload button -->
        	  <?php } else { ?>
        		<input type="button" id="image-upload" class="button image-upload" value=<?php esc_html_e("Browse", "wdp-plugin");  ?> style="display:none;" >
        		<?php } ?>

        	  </p>
        	  <p>

            <!-- If there is no image set, create an empty html container for the image and a hidden remove button -->
          	<?php if (!isset ($displayimage)) { ?>
          	<div class="image-show"></div>
          	<input type="hidden" value="<?php echo $post->ID; ?>" id="remove-hidden"> 
          	<input type="button" id="remove" class="button image-remove" value=<?php esc_html_e("Remove", "wdp-plugin")?> style="display:none; ">

            <!-- If there is an image set, create the html container & display the image and a remove button -->
            <?php } else { ?>
            <div class="image-show"><img src=<?php if (isset ($meta)) { echo $displayimage; } else { esc_html_e("empty", "wdp-plugin"); }  ?> class="image-true" id="image-true" style="max-width: 250px;"></div>
            <input type="hidden" value="<?php echo $post->ID; ?>" id="remove-hidden"> 
        	  <input type="button" id="remove" class="button image-remove" value=<?php esc_html_e("Remove", "wdp-plugin")?>>
        	  </p>
            <?php } 

        }

        /* Delete the post meta for a portfolio item based on it's ID. */
        public function wdp_delete_meta() {
        	  global $wpdb;
        	  $intvalue = intval( $_POST['value'] );

            delete_post_meta(  $intvalue,'_mobile_image_id' );

        	  wp_die(); 

        }

        /* Save the information stored in the mobile-image metabox */
        public function mobile_image_save( $post_id ) {  
  
          	// Verify nonce if set
            if (isset($_POST['submit_image'])) {
                $image_nonce = $_POST['submit_image'];
            }
          	if (isset($image_nonce)) {
          	    if ( !wp_verify_nonce( $image_nonce, 'submit_image_nonce') ) {
          		  return $post_id; 
          	    }
            }

        	  // Check autosave
        	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        		    return $post_id;
        	  }

           // Check the user's permissions
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }

            // Check for and sanitize user input
            if ( ! isset( $_POST['_mobile_image_id'] ) ) {
                return $post_id;
            }

           	// Sanitised meta key values
        	  $old = esc_url(get_post_meta( $post_id, '_mobile_image_id', true ));
        	  $new = esc_url($_POST['_mobile_image_id']);

            // Update the meta fields in the database, or clean up after ourselves
           	if ( $new && $new !== $old ) {
        		    update_post_meta( $post_id, '_mobile_image_id', $new );
        	  } 
            elseif ( '' === $new && $old ) {
        		    delete_post_meta( $post_id, '_mobile_image_id', $old );
        	  }

        }

        /* Function to output the contents of Project Link meta box */
        public function wdp_projects_url($post) {

            // Create a nonce field for submitting the image
        	  wp_nonce_field( 'url_metabox_nonce', 'submit_url' );

        	 // if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
        	  $url = get_post_meta($post->ID, '_my_url', true);
            ?>

            <!-- Build the html fields within the Project Link metabox -->
            <p>
            <input style="width: 100%" type="text" name="my_url" value="<?php echo esc_url( $url ); ?>" size="30" class="regular-text" />
            </p>
            <?php

        }

        /* Save the information stored in the Project Link metabox */
        public function my_url_save_metabox( $post_id ) {
           
            // Verify nonce if set
            if (isset($_POST['submit_url'])) {
                $url_nonce = $_POST['submit_url'];
            }
            if (isset($nonce)) {
                if ( !wp_verify_nonce( $url_nonce, 'url_metabox_nonce') ) {
                return $post_id; 
                }
            }

            // Check autosave
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            
            // Check the user's permissions
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            // Check for and sanitize user input
            if ( ! isset( $_POST['my_url'] ) ) {
                return;
            }

            // Sanitised meta key values
            $url = esc_url_raw( $_POST['my_url'] );

            // Update the meta fields in the database, or clean up after ourselves
            if ( empty( $url ) ) {
                delete_post_meta( $post_id, '_my_url' );
            } 
            else {
              update_post_meta( $post_id, '_my_url', $url );
            }

        }

    } // end class WDP_Plugin_CPT

}

/* If WDP_Plugin_CPT class exists, instantiate the class */
if(class_exists('WDP_Plugin_CPT')) 
{
    // Installation and uninstallation hooks
    // register_activation_hook(__FILE__, array('Web_Developers_Portfolio', 'activate'));
    // register_deactivation_hook(__FILE__, array('Web_Developers_Portfolio', 'deactivate'));

    // instantiate the plugin class
    $wp_plugin_template = new WDP_Plugin_CPT();
     
}
