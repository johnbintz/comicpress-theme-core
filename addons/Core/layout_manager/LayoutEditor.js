var LayoutEditor = Class.create({
  'ratio': 1.25,
  'sections': [
    [ 'header', .1 ],
    [ 'comic',  .3 ],
    [ 'blog',   .5 ],
    [ 'footer', .1 ]
  ],
  'initialize': function(container) {
    this.container = container; 
    
    this.width = this.container.getDimensions()['width'];
    this.height = Math.floor(this.container.getDimensions()['width'] * this.ratio);
    
    var i, il;   
    this.section_handles = [];
    for (i = 0, il = this.sections.length; i < il; ++i) {
      var sh = Math.floor(this.height * this.sections[i][1]);
      var section = new Element("div", { 'style': "height: " + sh + "px", "class": this.sections[i][0] });
      var inside = new Element("div");
      section.insert(inside);
      this.container.insert(section);
      this.section_handles.push(section);
    }
    
    this.sidebar_handles = {};
    var myThis = this;
    $w('left right').each(function(which) {
      myThis.sidebar_handles[which] = new Element("div", { 'style': 'position: absolute; z-index: 1' });
      var inside = new Element("div");
      myThis.sidebar_handles[which].insert(inside);
      myThis.container.insert(myThis.sidebar_handles[which]);
    });
  },
  'register_info': function(info) {
    this.info = info;
    this.info.onchange = this.draw;
  },
  'draw': function() {
    this.draw_sidebars();  
  },
  'draw_sidebars': function() {
    var myThis = this;
    $w('left right').each(function(field) {
      if (myThis.info.sidebars[field]) {
        var fi = myThis.info.sidebars[field];
        var t = myThis.section_handles[fi[0]].viewportOffset()['top'];
        var h = 0;
        var i;
        for (i = fi[0]; i <= fi[1]; ++i) {
          h += myThis.section_handles[i].getDimensions()['height'];
        }
        var w = Math.floor((myThis.info.widths[field] / myThis.info.widths.body) * myThis.width);
        var l;
        switch (field) {
          case 'left':
            l = myThis.container.viewportOffset()['left'];
            break;
          case 'right':
            l = myThis.container.viewportOffset()['left'] + myThis.width - w;
            break;
        }
        var field_map = {
          'top': t,
          'left': l,
          'width': w,
          'height': h
        };
        for (param in field_map) {
          myThis.sidebar_handles[field].style[param] = field_map[param];
        }
      }
    });
  }
});

var LayoutInfo = Class.create({
  'sidebars': {
    'left': [1, 3], 'right': [2, 3]
  },
  'widths': {
    'body': 800, 'left': 200, 'right': 175
  },  
  'change': function(group, detail, value) {
    if (this[group]) {
      if (this[group][detail]) {
        this[group][detail] = value;
        this.onchange(); 
      } 
    }
  }
});

Event.observe(window, 'load', function() {
  if ($('layout-editor-container')) {
    var l = new LayoutEditor($('layout-editor-container')); 
    
    var info = new LayoutInfo();
    l.register_info(info);
    l.draw();
  }
});
