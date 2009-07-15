<?php

class ComicPressAddonBookmarkWidget extends ComicPressAddon {
  function init($comicpress) {
    wp_register_sidebar_widget('comic-bookmark', __('Comic Bookmark', 'comicpress'), array(&$this, 'render_widget'), array('description' => __('Let your readers save their place via a cookie.', 'comicpress')));

    $this->comicpress = $comicpress;

    add_action('wp_head', array(&$this, 'wp_head'));
    $this->comicpress->additional_javascripts[] = '/js/bookmark.js';
  }

  function wp_head() {
    $last_comic = $this->comicpress->get_last_comic(); ?>
      <script type="text/javascript">
        var imgTag      = '<?php bloginfo('template_directory'); ?>/images/1.gif';  //add tag image.
        var imgClearOff = '<?php bloginfo('template_directory'); ?>/images/3a.gif'; //no comic tagged, clear not possible
        var imgGotoOff  = '<?php bloginfo('template_directory'); ?>/images/2a.gif'; //no comic tagged, goto not possible
        var imgClearOn  = '<?php bloginfo('template_directory'); ?>/images/3.gif';  //clear a tag, shows when comic previously tagged
        var imgGotoOn   = '<?php bloginfo('template_directory'); ?>/images/2.gif';  //shows when a comic is tagged  
        var imgInfo     = '<?php bloginfo('template_directory'); ?>/images/4.gif';  //img that displays the help
        var comicDir    = '/';                                               //alter this if you run multiple comics in different directories on your site.

        var comicPermalink = '<?php echo get_permalink($last_comic->ID) ?>';
        
        var isHome = <?php echo is_home() ? "true" : "false" ?>;
        var isSingle = <?php echo (is_single() && $this->comicpress->in_comic_category()) ? "true" : "false" ?>;
      </script>
    <?php
  }

  function render_widget() {
    ?>
    <div class="comic-bookmark">
      <script type="text/javascript">writeBookmarkWidget()</script>
    </div>
    <?php
  }
}

?>