jQuery(document).ready(function ($) {

	jQuery('#remove').on('click', function(){

		var $postID = jQuery('#remove-hidden').val(); 
		var data = {
			'action': 'wdp_delete_meta',
			'value': $postID
		};
		
	});
	
});