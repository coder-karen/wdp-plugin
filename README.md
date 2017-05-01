# Web Developer's Portfolio 
**Contributors:** wpkaren  
**Donate Link:** http://karenattfield.com/giving/
**Tags:** portfolio, post type, images, shortcode  
**Requires at least:** 4.4  
**Tested up to:** 4.7.4  
**Stable tag:** 0.1.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Custom portfolio solution including front-end display through shortcodes.


## Description 

Web Developer's Portfolio is a plugin designed to showcase screenshots from both desktop and mobile devices for each portfolio listing. Portfolio items are displayed on  any page of your choice using shortcodes.

This plugin works by creating a custom post type for portfolio items, and allows you to upload two separate images per portfolio item - one for a desktop screenshot and one for a mobile screenshot. Through the use of shortcodes you can then display portfolio items on a page of your choice - displaying either a desktop screenshot only, mobile screenshot only, or both. 

The output is styled with Flexbox, with an excerpt and button to the project page of your choice. You can also set the portfolio items to be publicly-queryable, meaning you can add single-portfolio.php or archive-portfolio.php templates to your site to customize the design of your individual portfolio pages.

Note: If you intend to use the default styling provided by the shortcodes it is strongly recommended that you utilize full-width pages. Extra columns or ultra narrow screen widths will mess up the styling (though you are welcome to modify the styling to fit your needs).


### Features

* **Project Link:** Add a link to the page you'd like your portfolio item to link to. 
* **Mobile Image:** A second featured image upload option for each portfolio item.
* **Shortcodes** Two different shortcodes to display either individual portfolio items by ID or all portfolio items.
* **Item ordering** You can set the order number for each portfolio item.
* **Rich text excerpts:** customized excerpt boxes allow for the addition of html elements as with the normal post editor.



## Installation 


1. Upload `wdp-plugin` to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Web Developers Portfolio settings option in the admin to set whether you'd like each portfolio item to be available on a page of it's own by default.
1. Head to the 'Portfolio' menu in the admin and start building your portfolio.



## Frequently Asked Questions 


### What are the shortcodes? 

[wd_portfolio] will output all portfolio items (images and excerpts, styled using Flexbox).
[wd_portfolio_single id="{id}"] (for example [wd_portfolio_single id="5"]) will output the portfolio item with the id specified (images and excerpt, styled using Flexbox).


### What is the point of the content editor on the portfolio editor page? 

While the purpose of this plugin is to enable you to display portfolio items via shortcodes linking to custom pages, you may choose to create custom pages on your site for your portfolio items based on the content you add directly in the portfolio editor. In the Wordpress Admin Menu, you will see a setting called ‘Web Developers Portfolio Plugin’ (Administrator-level access only). If you check the box in this settings page it will enable you to view each portfolio listing as a page. You will need to get your hands dirty with code and create a single-portfolio.php template in order to customize the page layout or an archive-portfolio.php template for an archive page – alternatively you can use the default portfolio page that is created for each portfolio item and make adjustments with php or css, depending on your theme / template.

The content within the content field will be what displays on the custom portfolio page created by this post-type.


### How can I customize the button colour? 

Add this to your child theme's style sheet:

// Main button background:
.wdp-btn {
background: #yourhexvaluehere !important; // eg. background: #27a6bc !important;
}

// On hover and active button background
.wdp-btn:hover, .wdp-btn:active {
background: #yourhexvaluehere !important; // eg. background: #52B8C9 !important;
}


### What are the recommended image sizes? 

For desktop screenshots, aim for an image ratio of width:height = 1.5:1 (for example 2550px x 1700px). 

For mobile screenshots, aim for an image ratio of width:height = 1:2 (for example 250px x 500px).


### The excerpt is too long? 

WDP plugin is best suited to display on full-width pages, with reasonable excerpts (not too long). Though excerpts are capped at 100 words, your site's font size may mean this can distort the layout of the preview. Experiment with excerpt sizes until you find the display that works best for your site.


### The mobile image doesn't sit quite right with the desktop image? 

The mobile screenshot is designed to sit over the bottom right of the desktop screenshot, with an overlap into the white space below. Depending on the size of the image you've uploaded, as well as the width of the screen space available (full-width recommended), this might not sit perfectly. The recommended image proportion for the mobile screenshot is width: height = 1:2 (height double the width). If you want to increase the width of the displayed mobile image (the height is automatic based on the width) add the following to your child themes style sheet:

.wdp-mobile-preview {
	max-width: yourwidthvalue !important; // adjust appropriately, for example 20%, 10em, etc.
}


### Do I need to use the shortcodes? 

No, you can use this plugin to create the portfolio custom post type and customize the portfolio page output as you wish through single-portfolio.php and archive-portfolio.php.


### Why was this plugin created? 

I had a need to display both desktop and mobile screenshots for individual portfolio items, and didn't want to have to hand-code every new item into the page template. I created this plugin to allow the process to be quick, efficient and scalable.




## Screenshots 

### 1. Edit portfolio item admin screen
See screenshot-1.png

### 2. Portfolio item management admin screen
See screenshot-2.png

### 3. Portfolio settings admin screen
See screenshot-3.png

### 4. Screenshot of portfolio shortcode output on front-end
See screenshot-4.png



## Changelog 


### 0.1.0 
Initial release

