<?php
/*
* Admin-specific functionality of the plugin
* The settings options and portfolio listings page
*/


if ( ! class_exists( 'WDP_Admin' ) ) {

    /**
     * Main WDP_Admin class
     */
    class WDP_Admin {

    	 /**
        * Initialize the class and set its properties.
        */
		public function __construct(){

			add_action( 'admin_menu', array($this, 'wdp_add_admin_menu' ));
			add_action( 'admin_init', array($this, 'wdp_settings_init' ));
			add_filter('manage_portfolio_posts_columns', array($this,'portfolio_table_head'), 5);
            add_action('manage_portfolio_posts_custom_column', array($this,'portfolio_table_content'), 5, 2);
            add_filter('manage_portfolio_posts_columns', array($this,'column_order'));
            add_filter('pre_get_posts', array($this, 'order_portfolio_items'));

		}

		/* Add Web Developers Portfolio settings option in menu */
		public function wdp_add_admin_menu(  ) { 

			add_menu_page( 'Web Developers Portfolio', 'Web Developers Portfolio', 'manage_options', 'web_developers_portfolio', array($this,'wdp_options_page') );

		}

		/* Initialize settings for settings page */
		public function wdp_settings_init(  ) {

			register_setting( 'pluginPage', 'wdp_settings' );

			add_settings_section(
				'wdp_pluginPage_section', 
				__( 'Portfolio page visibility', 'wdp-plugin' ), 
				array($this,'wdp_settings_callback'), 
				'pluginPage'
			);

			add_settings_field( 
				'wdp_checkbox_field', 
				__( 'Select for publicly visible portfolio pages', 'wdp-plugin' ), 
				array($this,'wdp_checkbox_renderer'), 
				'pluginPage', 
				'wdp_pluginPage_section' 
			);	


			register_setting('pluginPage', 'wdp-button-text', array($this,'wdp_validate_options'));

			add_settings_section('wdp_pluginPage_button',
				__('Portfolio url button display', 'wdp-plugin' ),
				array($this, 'wdp_button_text'),
					'pluginPage'
				);

			add_settings_field('wdp_button_text_field',
				__('Input button text here:', 'wdp-plugin'),
				array($this, 'wdp_button_text_renderer'),
				'pluginPage',
				'wdp_pluginPage_button'
				);


		}

		/* Create input field for checkbox */
		public function wdp_checkbox_renderer(  ) { 
	
			$options = get_option( 'wdp_settings' );
			?>
			<input type='checkbox' name='wdp_settings[wdp_checkbox_field]' <?php if ($options != '') {echo "checked"; } else { echo ""; } ?> value='1'>
			<?php

		}

		/* Create callback checkbox fields */
		public function wdp_settings_callback(  ) { 

			esc_html_e( 'Select checkbox to allow portfolio items to be visible as individual portfolio pages (also allowing for customisation with single-portfolio.php template). This will set the "publicly_queryable" and "has_archive" post-type parameters to true for portfolio items. The default - unchecked - allows portfolio items only to be visible through shortcodes. If checked, remember to go to Settings->Permalinks and choose "Post Name"', 'wdp-plugin' );

		}

		/* Create callback checkbox fields */
		public function wdp_button_text(  ) { 

			esc_html_e( 'Type the text you would like displayed within the buttons linking to your custom project url (default is "View Project").', 'wdp-plugin' );

		}

		/* Create input field for button text */
		public function wdp_button_text_renderer(  ) { 
	
			$options = (get_option( 'wdp-button-text' ));

			if (($options['wdp_button_text_field'] != '') && $options != false) {
				$options_string = implode(" ", $options);
				$placeholder = "placeholder='" . $options_string . "'";
			}
			else {
				$options_string = __('View Project', 'wdp-plugin'); 
				$placeholder = 'placeholder="' . $options_string . '"';
			}

			echo '<input name="wdp-button-text[wdp_button_text_field]" id="wdp_button_text_field" type="text" value="' . $options_string .  '"' . $placeholder . '/>';
	
		}

		// Sanitize and validate text input
		public function wdp_validate_options($input) {

			$input['wdp_button_text_field'] =  wp_filter_nohtml_kses($input['wdp_button_text_field']);	

			return $input; // return validated input

		}



		/* Creating the settings page */
		public function wdp_options_page(  ) { 

			?>
			<form action='options.php' method='post'>
			<h2><?php esc_html_e( 'Web Developers Portfolio Plugin', 'wdp-plugin' );?></h2>
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

			</form>
			<h2><?php esc_html_e( 'Using the shortcodes', 'wdp-plugin' );?></h2>
			<p>
			<?php
			$shortcode1 = '[wd_portfolio_single id="id"]';
			$shortcode2 = '[wd_portfolio_single id="5"]';
			printf(__('To display a single portfolio item, use %1$s for example %2$s.'), $shortcode1, $shortcode2); 
			?>
			<br/>
			<?php
			$shortcode3 = '[wd_portfolio]';
			printf(__('To display all portfolio items, use %1$s.'), $shortcode3);
			?></p>
			<?php


		}


		/* Adding new labels to the portfolio item listing page */
        public function portfolio_table_head($defaults) {

            $defaults['wdp_portfolio_id'] = __('ID');
            $defaults['wdp_portfolio_order'] = __('Order');
            $defaults['wdp_portfolio_url'] = __('URL');
            return $defaults;

        }


        /* Populating the new columns */
        public function portfolio_table_content($column_name, $id){

            if($column_name === 'wdp_portfolio_id') {
                echo $id;   
            }

             if($column_name === 'wdp_portfolio_order') {
                echo get_post_field('menu_order', $id);
            }

            if($column_name === 'wdp_portfolio_url') {
                $url = get_post_field( '_my_url', $id );
                printf(__('<a href="%1$s" target="_blank">%1$s</a>'), $url);
            }

        }

        /* Rearranging the order of the columns */
        public function column_order($defaults) {  

            $new = array();
            // save the columns:
            $my_id = $defaults['wdp_portfolio_id'];  
            $order = $defaults['wdp_portfolio_order'];  
            $url = $defaults['wdp_portfolio_url'];  

            //remove the columns
            unset($defaults['wdp_portfolio_id']);   
            unset($defaults['wdp_portfolio_order']);   
            unset($defaults['wdp_portfolio_url']);   

            foreach($defaults as $key=>$value) {

                if($key=='date') {  

                	// place each column before the 'date' column, in the following order:
                    $new['wdp_portfolio_url'] = $url;  
                    $new['wdp_portfolio_id'] = $my_id; 
                    $new['wdp_portfolio_order'] = $order;  
                }  

                $new[$key]=$value;

            }  

            return $new;  
        }

        /* Order the portfolio items by date on the listings page */
        public function order_portfolio_items( $query ) {

    		if ($query->get('post_type') == 'portfolio') {
      			
      			$query->set('orderby', 'date');
     			$query->set('order', 'DESC');

    			}
		} 

	}

}	


/* If WDP_Admin class exists, instantiate the class */
if(class_exists('WDP_Admin')) {

    $wp_plugin_template = new WDP_Admin();
     
}