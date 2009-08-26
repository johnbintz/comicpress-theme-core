var FloatedDivConstructor = Class.create({
  'generate_html': function(layout) {
    var output = [];
    output.push('<div id="container">');

    var areas = $w("header comic body footer");
    var i, il;

    var indent = 1;

    var range = null;
    if (layout.left) {
      if (layout.right) {
        range[0] = Math.min(layout.left[0], layout.right[0]);
        range[1] = Math.max(layout.left[1], layout.right[1]);
      } else {
        range = layout.left;
      }
    } else {
      if (layout.right) {
        range = layout.right;
      }
    }

    for (i = 0, il = areas.length; i < il; ++i) {
      if (range) {
        if (range[0] == i) {
          output.push("  ".times(indent) + '<div id="sidebar-container">');
          indent++;
        }
        $w("left right").each(function(field) {
          if (layout[field]) {
            if (layout[field][0] == i) {
              output.push("  ".times(indent) + '<div id="' + field + '-sidebar"><?php echo $' + field + '_sidebar ?></div>');
            }
          }
        });
      }

      output.push("  ".times(indent) + '<div id="' + areas[i] + '"><?php echo $' + areas[i] + ' ?></div>');

      if (range) {
        if (range[1] == i) {
          output.push("  ".times(indent) + '<br class="clear" />');
          indent--;
          output.push("  ".times(indent) + '</div>');
        }
      }
    }

    output.push('</div>');

    return output.join("\n");
  },
  'generate_css': function(info) {
    var output = [];
    var include_container = false;

    $w('left right').each(function(field) {
      if (info[field]) {
        include_container = true;
        output.push('#' + field + '-sidebar { float: ' + field + '; display: inline; width: ' + info[field].width + 'px }');
      }
    });

    if (include_container) {
      output.unshift('#sidebar-container { overflow: hidden }');
      output.push('.clear { clear: both }');
    }

    return output.join("\n");
  }
});