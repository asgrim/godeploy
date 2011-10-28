/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */

var active_submit_button = null;
var form;
$(document).observe("dom:loaded", function(){

	$$(".processing_btn").each(function(el) {
		
		// Set the active submit button to the currently clicked button for use later on
		$(el).observe("mousedown", function() {
			setActiveButton(this);
		});
		
		form = $(el).up("form");

		// Get the colour and the size from the current button
		var parent_wrapper = $(el).up(".wrapper");
		var wrapper_colour = parent_wrapper.className.match(/w_(\w{6})/);
		var inverted = ($(el).src.indexOf("inverted") != -1) ? "inverted/" : "";
		if (!wrapper_colour) wrapper_colour = ["", "ffffff"];
		var size = $(el).className.match(/size_(\w+)/);

		// Create a new image with the animation
		var new_img = document.createElement("img");
		new_img.src = "/images/buttons/" + size[1] + "/" + inverted + "processing-on-" + wrapper_colour[1] + ".gif";
		$(new_img).className = $(el).className;
		$(new_img).addClassName("hidden");
		$(el).up("li").insert(new_img);

	});
	
	
	// Smash an event onto the onsubmit of the form so we can catch it however it's submitted
	$(form).observe("submit", function() {

		// If the unique submit button isn't set, grab the first one and use that
		if (!$(active_submit_button))
		{
			active_submit_button = $(this).down(".processing_btn");
		}
		var submit_button = $(active_submit_button);

		// Hide the current button and show the new one
		$(submit_button).addClassName("hidden");
		$(submit_button).next().removeClassName("hidden");
		
		// Prevent the form being submitted again
		$(this).onsubmit = function() {};

	});

});

function setActiveButton(el)
{
	active_submit_button = el;
}