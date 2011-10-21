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

var slug_span, slug;
function initSlug(field)
{
	// add a span into the $field <li> so we can generage the slug on the fly
	if (!$("item_" + field)) return;

	slug_span = document.createElement("SPAN");
	slug_span.className = "slug";
	slug_span.id = field + "_slug_span";
	
	$(field).insert({"after": slug_span});
	$(field + "_slug_span").update('The slug will be: "<span id="' + field + '_slug"></span>"');
	updateSlug($(field));


	// update the slug when typing in the $field
	$(field).observe("keyup", function() {
		updateSlug(this);
	})
}

function updateSlug(field)
{
	var str = getSlugString(field.value);
	$(field.id + "_slug").update(str);
}

function getSlugString(str)
{
	str = str.toLowerCase(); // strtolower()
	str = str.replace(/[^a-zA-Z0-9\s]/g, ""); // dodgy chars
	str = str.replace(/^\s+|\s+$/g, ""); // trim()
	str = str.replace(/[\s+^$]/g, "-"); // repeated spaces
	return str;
}