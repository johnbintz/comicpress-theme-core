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
      var handle = new Element("div", { 'style': 'position: absolute; z-index: 1' });      
      var inside = new Element("div");
      
      var t = new Element("div", { 'class': 'top' });
      var b = new Element("div", { 'class': 'bottom' });
      
      handle.align_bottom = function() {
        var h = handle.getDimensions()['height'];
        b.style.top = h - 5;
      };
      
      handle.insert(t);
      handle.insert(b);      
      handle.insert(inside);
      myThis.container.insert(handle);
      
      var generate_handle_move = function(g) {
        return function(e) {
          Event.stop(e);
          
          var cy = handle.viewportOffset()['top'];
          var ch = handle.getDimensions()['height'];          
          var ny, nh;
          
          switch(g.className) {
            case 'top':
              ny = e.clientY;
              nh = ch + (cy - ny);
              break;
            case 'bottom': 
              nh = ch + (cy + ch - e.clientY);
              break;
          }
          if (nh < 5) { nh = 5; }
          handle.style.top = ny;
          handle.style.height = nh;

          handle.align_bottom();
        };
      };
      
      var snap_handle = function() {
        var ty = handle.viewportOffset()['top'];
        var by = ty + handle.getDimensions()['height'];
        
        var i, il;
        var h = 0;
        var closest = { 'top': null, 'bottom': null };
        for (i = 0, il = myThis.section_handles.length; i < il; ++i) {
          var distance = { 'top': null, 'bottom': null };
          distance.top = Math.abs(ty - h);
          h += myThis.section_handles[i].getDimensions()['height'];
          distance.bottom = Math.abs(by - h);
          for (field in closest) {
            if (closest[field] == null) { closest[field] = [distance[field], i]; }
            if (distance[field] < closest[field][0]) {
              closest[field] = [distance[field], i]; 
            }
          }
        }
        top.console.log(closest);
        myThis.info.change('sidebars', which, [closest.top[1], closest.bottom[1]]);
      };
      
      inside.observe('mousedown', function(e) { Event.stop(e); });
      handle.observe('mousedown', function(e) { Event.stop(e); });
      
      var t_handle = generate_handle_move(t);
      var b_handle = generate_handle_move(b);
      
      t.observe('mousedown', function(e) {
        Event.observe(document.body, 'mousemove', t_handle);
      });
      b.observe('mousedown', function(e) {
        Event.observe(document.body, 'mousemove', b_handle);         
      });
      Event.observe(document.body, 'mouseup', function() {
        Event.stopObserving(document.body, 'mousemove', t_handle);
        Event.stopObserving(document.body, 'mousemove', b_handle);
        snap_handle();
      });
      
      myThis.sidebar_handles[which] = handle;
    });
  },
  'register_info': function(info) {
    this.info = info;
    var myThis = this;
    this.info.onchange = function() {
      myThis.draw_sidebars();  
    }
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
        myThis.sidebar_handles[field].align_bottom();
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
