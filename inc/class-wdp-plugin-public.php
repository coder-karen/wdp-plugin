<?php 
/*
* Public facing functionality of the plugin
* The shortcode display
*/


if ( ! class_exists( 'WDP_Plugin_Public' ) ) {

    /**
     * Main WDP Plugin Public class
     */
    class WDP_Plugin_Public {


        /**
        * Initialize the class and set its properties.
        */
        public function __construct() {

            add_filter('pre_get_posts', array($this, 'posts_for_current_author'));
            add_action( 'after_setup_theme', array( $this, 'addImageSupportPortfolio' ), 11 );
            add_action('admin_menu', array($this, 'wdp_remove_excerpt_admin'));
            add_action('add_meta_boxes', array($this, 'wdp_create_excerpt_admin'));
            add_action('edit_form_after_title', array($this, 'wdp_excerpt_top'));
            add_shortcode('wd_portfolio_single', array($this,'wdp_shortcode_single'));
            add_shortcode('wd_portfolio', array($this,'wdp_shortcode'));

        }

        /* Removes the excerpt box */
        public function wdp_remove_excerpt_admin() { 

            remove_meta_box('postexcerpt', 'portfolio', 'normal');  

        }

        /* Creates a new excerpt box */
        public function wdp_create_excerpt_admin() { 

            if (get_post_type() == 'portfolio') {
                add_meta_box( 'portfolioexcerpt',
                __( 'Portfolio Excerpt', 'wdp-plugin' ),
                array ( $this, 'show_excerpt_box' ),
                null,
                'top', // a bespoke context
                'high' 
                );
            } // end if get post type

        }

        /* Displays the new excerpt box */
        public static function show_excerpt_box( $post ) { 

            ?><label class="screen-reader-text" for="excerpt">
            <?php esc_html_e( 'Excerpt', 'wdp-plugin' ) ?></label>
            <?php
            wp_editor(
                htmlspecialchars_decode( $post->post_excerpt ),
                'excerpt', // use default name 'excerpt'
                array (
                'textarea_rows' => 15,
                'media_buttons' => FALSE,
                'teeny' => FALSE,
                'tinymce' => TRUE
                )
            );

        }

        /* Moves the excerpt to the top of the admin page */
        public function wdp_excerpt_top() { 
           
            if (get_post_type() == 'portfolio') {
                global $post, $wp_meta_boxes;
                do_meta_boxes( get_current_screen(), 'top', $post );
                unset($wp_meta_boxes['post']['top']);
            } // end if get post type

        }

        /* Add theme support for post thumbnails in portfolio post type */
        public function addImageSupportPortfolio() {
          
            $postTypes = get_theme_support( 'post-thumbnails' );
           
            if( $postTypes === false ) {
                add_theme_support( 'post-thumbnails', array( 'portfolio') );  
            }             
            
            elseif( is_array( $postTypes ) ) {
                $postTypes[0][] = 'portfolio';
                add_theme_support( 'post-thumbnails', $postTypes[0] );
            }

        }

        /* Allows an excerpt limit to be set (in number of words) */
        public static function excerpt($limit) { 

            $excerpt = explode(' ', get_the_excerpt(), $limit);
            if (count($excerpt)>=$limit) {
                array_pop($excerpt);
                $excerpt = implode(" ",$excerpt).'...';
            } 
            else {
                $excerpt = implode(" ",$excerpt);
            } 
            $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
            return $excerpt;

        }

        /* Allowing any number of portfolio items to be displayed by order attributed */
        public function posts_for_current_author($query) {  
         
            if ($query->get('post_type') == 'portfolio') {
                $query->set('orderby', 'menu_order');
                $query->set('order', 'ASC');
                $query->set( 'posts_per_page', '-1' );
            }
          
          return $query;

        }

        /* Function to create the html output after calling the shortcodes */
        public function wdp_shortcode_function($atts, $content = null) {

            if (isset ($atts)) {
                $atts = shortcode_atts(
                    array(
                        'id' => '',
                    ), $atts
                );
            }

            $args = array(
                'post_type' => 'portfolio',
                'post_status' => 'publish'
            );

            // Define the variable that we will store all content to be returned.
            $portfoliopreview = "";

            $loop = new WP_Query($args);
            if ($loop->have_posts() ) {
                while ($loop->have_posts() ) {
                    $loop->the_post();
                    
                    // If the id of the post in the loop matches the id given in the shortcode
                    // or there is no id given in the shortcode
                    if ((get_the_ID() == $atts['id']) || !isset($atts))  {

                        $theTitle = esc_html(get_the_title());
                        $theExcerpt = WDP_Plugin_Public::excerpt(70);
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->ID ), 'large', false);
                        $imageurl = esc_url( $image[0] );
                        $mobileimage = get_post_custom();
                        $custom = get_post_custom();


                        if ((!isset($imageurl) ) && (!isset($mobileimage['_mobile_image_id'])) && (!isset($custom['_my_url']) ))  {

                            return;

                        }

                        else {

                            // Begin building the html output
                            $portfoliopreview = '<div class="wdp-port-snippet"><div class="wdp-work-preview">';

                            //Add an extra closing div if there are no images but there is a url.
                             if ((!isset($imageurl) ) && (!isset($mobileimage['_mobile_image_id'])) && (isset($custom['_my_url']) ))  {
                                $portfoliopreview .=  '</div>';

                             }

                            // If the main featured image is set, build the img container and display the image
                            if (isset($imageurl) ) {
                                $mainimageprint = sprintf( '<div class="wdp-preview-main"><img src="%1$s" alt="%2$s" />', $imageurl, esc_html__( 'Main preview image', 'wdp-plugin' ) );
                                $portfoliopreview .= $mainimageprint;

                                // If the mobile image isn't set, whilst the featured image is set
                                if (!isset($mobileimage['_mobile_image_id']))  {
                         
                                    // Build the closing image html and open the title and excerpt html
                                    $portfoliopreview .= '</div></div>';
                                }  
                            } // end if isset $imageurl
         

                            // If the mobile image is set
                            if (isset($mobileimage['_mobile_image_id']))  {

                                $mobileimageurl = esc_url( $mobileimage['_mobile_image_id'][0] );

                                // If the mobile image is set but the featured image isn't, build the html output
                                if (isset($mobileimage['_mobile_image_id']) && !isset($imageurl)) {
                                    $mobileimageprint =  sprintf( '<div class="wdp-mobile-preview-alt"><div class="wdp-mobile-main-alt"><img src=%1$s></div><div class="wdp-mobile-bottom-alt"></div></div>', $mobileimageurl);
                                    $portfoliopreview .= $mobileimageprint;

                                    // Build the closing image html
                                    $portfoliopreview .= '</div>';
                                }
            
                                 // If both the mobile image and featured images are set, build the html output
                                if (isset($mobileimage['_mobile_image_id']) && isset($imageurl)) {
                                    $allimageprint = sprintf( '<div class="wdp-mobile-preview"><div class="wdp-mobile-main"><img src=%1$s></div><div class="wdp-mobile-bottom"></div></div>', $mobileimageurl);
                                    $portfoliopreview .= $allimageprint;

                                    // Build the closing image html
                                    $portfoliopreview .= '</div></div>';
                                }  
                            } // end if isset mobile image id
                 
                            // Open the title and excerpt html
                            $titleexcerpt = sprintf( '<div class="wdp-work-blurb"><h3 class="wdp-section-title">%1$s</h3><p>%2$s</p>' , $theTitle, $theExcerpt);
                            $portfoliopreview .= $titleexcerpt;

                            // Display button linking to custom url, if custom url is set
                            if (isset($custom['_my_url']) ) {
                                $options = get_option( 'wdp-button-text' );
                                $button_text = implode(" ", $options);

                                if ($button_text == '') {                         
                                    $button_text = esc_html__( 'View Project', 'wdp-plugin' ); 
                                }

                                $printbutton = sprintf( '<div class="wdp-featured-cta"><span class="wdp-buttons"><a class="wdp-btn" href="%1$s">%2$s</a></span></div>', $custom['_my_url'][0], $button_text) ;
                                $portfoliopreview .= $printbutton;
                            }

                            // Build the closing html
                            $portfoliopreview .= '</div><div style="clear: both;"></div></div>';

                            

                            //Echo the completed html output string
                            echo $portfoliopreview;

                        } // end if images and or link is set

                    } // end if atts id = get the id

                } // end while have posts loop

            } // end if have posts loop
      

            wp_reset_postdata();

        }

        /* wdp_shortcode function - runs wdp_shortcode_function with no atts and prints all portfolio items */
        public function wdp_shortcode( $content = null) {

            ob_start();
            $this->wdp_shortcode_function($content = null);
            return ob_get_clean();

        }

        /* wdp_shortcode_single function - runs wdp_shortcode_function with atts (id) and prints matching portfolio item */
        public function wdp_shortcode_single($atts, $content = null) {
        
            ob_start();
            $this->wdp_shortcode_function($atts, $content = null);
            return ob_get_clean();
          
        }

    } // end WDP_Plugin_Public class

}


 /* If WDP_Plugin_Public class exists, instantiate the class */
if (class_exists('WDP_Plugin_Public')) {

    $wp_plugin_template = new WDP_Plugin_Public();
 
}