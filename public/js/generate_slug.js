// JavaScript Document

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