<?php

class ComicPressAddonBookmarkWidget extends ComicPressAddon {
  function init() {
    wp_register_sidebar_widget('comic-bookmark', __('Comic Bookmark', 'comicpress'), array(&$this, 'render_widget'), array('description' => __('Let your readers save their place via a cookie.', 'comicpress')));
  }

  function render_widget() {
    $url_root = bloginfo('template_directory') . '/' . substr(dirname(__FILE__), strlen(realpath(get_stylesheet_directory())));
    var_dump($url_root);
  }
}

?>