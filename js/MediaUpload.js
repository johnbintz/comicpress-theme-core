jQuery(function() {
  jQuery('.media-item').each(function() {
    var item = this;
    jQuery('.savesend', item).each(function() {
      jQuery(this).prepend(jQuery('<input type="submit" name="save" value="Save changes" />').addClass('button'));
    });
    
    var show_insert = function(t) {
      jQuery('input[name*=send]', item)[(t.value == 'none') ? 'show' : 'hide']();
    };
    
    jQuery('input[name*=comic_image_type]', item).bind('click', function() { show_insert(this); });
    var type = jQuery('input[name*=comic_image_type][checked]', item).get(0);
    if (type) {
      jQuery('.filename.new', item).append(jQuery('<strong />').text(' (' + jQuery.trim(jQuery(type).parent().text()) + ')'));
      show_insert(type);
    }
  });
});