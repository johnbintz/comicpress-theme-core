<?php

class ComicPressAddonBookmarkWidget extends ComicPressAddon {
  var $name = "Bookmark Widget";

  function init($comicpress) {
    wp_register_sidebar_widget('comic-bookmark', __('Comic Bookmark', 'comicpress'), array(&$this, 'render_widget'), array('description' => __('Let your readers save their place via a cookie.', 'comicpress')));

    $this->comicpress = $comicpress;

    add_action('wp_head', array(&$this, 'wp_head'));
    $this->comicpress->additional_javascripts[] = '/js/bookmark.js';
    
    wp_enqueue_script('prototype');
    wp_enqueue_script('cookiejar', get_template_directory_uri() . '/js/cookiejar.js', array('prototype'));
  }

  function wp_head() {
    $last_comic = $this->comicpress->get_last_comic(); ?>
      <script type="text/javascript">
        var image_root = '<?php bloginfo('template_directory'); ?>/images/';
      
        var button_images = {
          'clear-tag': {
            'off': '3a.gif', 'on': '3.gif'
          },
          'goto-tag': {
            'off': '2a.gif', 'on': '2.gif'
          }
        };
      
        var permalink = '<?php echo get_permalink($last_comic->ID) ?>';
        
        var BookmarkInfo = Class.create({
          'default': {
            'permalink': false
          },
          'initialize': function() {
            this.jar = new CookieJar({
              'expires': 60 * 60 * 24 * 31,
              'path': '<?php bloginfo('template_directory') ?>'
            });
          },
          'read': function() {
            var bookmark_info = this.jar.get('bookmark-info');            

            
            
            return bookmark_info;
          },
          'write': function(bookmark_info) {
            this.jar.put('bookmark-info', bookmark_info); 
            if (this.onWrite) { this.onWrite(bookmark_info); }
          }
        });
        
        Event.observe(window, 'load', function() {          
          var bookmark_info = new BookmarkInfo();
          var info = bookmark_info.read();
          
          var hrefs = {};
          $$('#comic-bookmark-holder a').each(function(a) {
            var name = $w(a.className).shift();
            hrefs[name] = a;
          });
          
          var set_goto_tag = function(i) {
            top.console.log(i);
            hrefs['goto-tag'].href = (i.permalink ? i.permalink : "#");
            [ 'goto-tag','clear-tag' ].each(function(which) {
              hrefs[which].select('img')[0].src = image_root + button_images[which][i.permalink ? "on" : "off"];
              top.console.log(i.permalink ? "on" : "off");
            });              
          };
          
          bookmark_info.onWrite = function(i) { set_goto_tag(i); }
          set_goto_tag(info);
        
          Event.observe(hrefs['tag-page'], 'click', function(e) {
            Event.stop(e);
            info.permalink = permalink;
            bookmark_info.write(info);
          });
          
          Event.observe(hrefs['clear-tag'], 'click', function(e) {
            Event.stop(e);
            info.permalink = false;
            bookmark_info.write(info);
          });
          
          Event.observe(hrefs['goto-tag'], 'click', function(e) {
            if (hrefs['goto-tag'].href == "#") { Event.stop(e); }
          });
        });
      </script>
    <?php
  }

  function render_widget() {
    ?>
    <div id="comic-bookmark-holder">
      <a href="#" class="tag-page"><img src="<?php bloginfo('template_directory'); ?>/images/1.gif" /></a>
      <a href="#" class="goto-tag"><img /></a>
      <a href="#" class="clear-tag"><img /></a>
      <a href="#" class="info-tag"><img src="<?php bloginfo('template_directory'); ?>/images/4.gif" </a>
    </div>
    <?php
  }
}

?>