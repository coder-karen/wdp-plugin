jQuery(document).ready(function ($) {

	// Instantiates the variable that holds the media library frame.
	var meta_image_frame;

	// Runs when the image upload button is clicked.
	$('.image-upload').click(function (e) {
		
		e.preventDefault();
		
		var meta_image = $('.meta-image');
		var meta_button = $('.image-upload');
		var meta_div = $('.image-show');	

		// If the frame already exists, re-open it.
		if (meta_image_frame) {
			meta_image_frame.open();
			return;
		}  

		// Sets up the media library frame
		meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			title: meta_image.title,
			button: {
				text: meta_image.button
			}
		});

		// Runs when an image is selected
		meta_image_frame.on('select', function () {

			// Creates a JSON representation of the attachment selection
			var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

			// Sends the attachment URL to our image input field
			meta_image.val(media_attachment.url);

			// Empties the 'image-show' div
			meta_div.empty();

			// Adds new image to 'image-show' div
			meta_div.prepend($('<img>',{src:media_attachment.url, style:'max-width: 100%;'}));

			// Makes the 'image-remove' and 'image-show' divs visible
			$('.image-remove').css("display", "inline-block");
			$('.image-show').show();

			// Hides the parent html container that mobile_image_id is in
			$('#_mobile_image_id').parent().hide();
		
		});

		// Opens the media library frame.
		meta_image_frame.open();
		
	});

	// Runs when the remove button is clicked.
	jQuery('#remove').click(function (e) {

		jQuery('.image-remove').hide();
		jQuery('.image-show').hide();
		jQuery('.image-upload').css("display", "inline-block");
		jQuery('.meta-image').val("");
		jQuery('#_mobile_image_id').css("display", "inline-block");
		jQuery('#_mobile_image_id').parent().show();
		jQuery("label[for=_mobile_image_id]").css("display", "inline-block");
	});
		

});



