var Storyline = {};

Storyline.get_order = function() {
	var order = []
	jQuery('#storyline-sorter .cp-category-info').each(function() {
		var matches = this.className.match(/category-([0-9\/]+)/);
		if (matches) { order.push(matches[1]); }
	});
	jQuery('input[name=cp[storyline_order]]').attr('value', order.join(','));
};

Storyline.setup = function() {
	jQuery('.cp-children').sortable({
		handle: 'span',
		cursor: 'move',
		placeholder: 'placeholder',
		forcePlaceholderSize: true,
		opacity: 0.4,
		stop: function(e, ui) {
			Storyline.get_order();
		}
	});
};

jQuery(function() { Storyline.get_order(); });