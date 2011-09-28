// JavaScript Document

$(document).observe("dom:loaded", function(){

	$$(".processing_btn").each(function(el) {

		// Get the colour and the size from the current button
		var parent_wrapper = $(el).up(".wrapper");
		var wrapper_colour = parent_wrapper.className.match(/w_(\w{6})/);
		if (!wrapper_colour) wrapper_colour = ["", "ffffff"];
		var size = $(el).className.match(/size_(\w+)/);
		
		// Create a new image with the animation
		var new_img = document.createElement("img");
		new_img.src = "/images/buttons/" + size[1] + "/processing-on-" + wrapper_colour[1] + ".gif";
		$(new_img).addClassName("hidden");
		$(el).up("li").insert(new_img);
		
		
		// Smash an event onto the onsubmit of the form so we can catch it however it's submitted
		$(el).up("form").observe("submit", function() {

			// YOYO
			var submit_button = $(this).down(".processing_btn");

			// Hide the current button and show the new one
			$(submit_button).addClassName("hidden");
			$(submit_button).next().removeClassName("hidden");
			
			// Prevent the form being submitted again
			$(this).onsubmit = function() {};

		});

	});

});