var FloatedDivConstructor = Class.create({
  'generate_html': function(layout) {
    var output = [];
    output.push('<div id="container">');


    var areas = $w("header comic body footer");
    var i, il;

    var indent = 1;
    var gi = function() { return "  ".times(indent); }
    var has_whole_sidebar = null;

    var range = null;
    if (layout.left) {
      if (layout.right) {
        range = [];

        var all_same = true;
        for (i = 0; i < 2; ++i) {
          if (layout.left[i] != layout.right[i]) { all_same = false; break; }
        }

        if (!all_same) {
          $w('left right').each(function(field) {
            if (!has_whole_sidebar) {
              if ((layout[field][0] == 0) && (layout[field][1] == 3)) {
                has_whole_sidebar = field;
              }
            }
          });
        }

        if (!has_whole_sidebar) {
          range[0] = Math.min(layout.left[0], layout.right[0]);
          range[1] = Math.max(layout.left[1], layout.right[1]);
        } else {
          switch (has_whole_sidebar) {
            case 'left': range = layout.right; break;
            case 'right': range = layout.left; break;
          }
        }
      } else {
        range = layout.left;
      }
    } else {
      if (layout.right) {
        range = layout.right;
      }
    }

    for (i = 0, il = areas.length; i < il; ++i) {
      if ((i == 0) && has_whole_sidebar) {
        output.push(gi() + '<div id="whole-sidebar-container">');
        indent++;
        output.push(gi() + '<div id="' + has_whole_sidebar + '-sidebar"><?php echo $' + has_whole_sidebar + '_sidebar ?></div>');
      }
      if (range) {
        if (range[0] == i) {
          output.push(gi() + '<div id="sidebar-container">');
          indent++;
        }
        $w("left right").each(function(field) {
          if (field != has_whole_sidebar) {
            if (layout[field]) {
              if (layout[field][0] == i) {
                output.push(gi() + '<div id="' + field + '-sidebar"><?php echo $' + field + '_sidebar ?></div>');
              }
            }
          }
        });
      }

      output.push(gi() + '<div id="' + areas[i] + '"><?php echo $' + areas[i] + ' ?></div>');

      if (range) {
        if (range[1] == i) {
          output.push(gi() + '<br class="clear" />');
          indent--;
          output.push(gi() + '</div>');
        }
      }
      if ((i == 3) && has_whole_sidebar) {
        output.push(gi() + '<br class="clear" />');
        indent--;
        output.push(gi() + '</div>');
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
      output.unshift('#sidebar-container, #whole-sidebar-container { overflow: hidden }');
      output.push('.clear { clear: both }');
    }

    return output.join("\n");
  }
});