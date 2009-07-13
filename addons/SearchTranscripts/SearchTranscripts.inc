<?php

/*
  Based on Search Custom Fields by Kaf Oseo (http://guff.szub.net/search-custom-fields/)
  Copyright (c) 2006 Kaf Oseo (http://szub.net)
*/

class ComicPressAddonSearchTranscripts extends ComicPressAddon {
  var $custom_template_default = 'search-transcript.php';

  function init($comicpress) {
    add_filter('posts_join', array(&$this, 'search_custom_join'));
    add_filter('posts_where', array(&$this, 'search_custom_where'));
    add_filter('search_template', array(&$this, 'search_custom_template'));
    wp_register_sidebar_widget('search-comic-reanscripts', __('Search Comic Transcripts', 'comicpress'), array(&$this, 'render_widget'), array('description' => __("Search all of your comic posts' transcripts", 'comicpress')));
    
    $this->comicpress = $comicpress;
  }

  function search_custom_join($join) {
    global $wpdb;
    if (is_search() && $this->is_search_key()) {
      $join = " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
    }
    return $join;
  }

  function search_custom_where($where) {
    global $wp_query, $wp_version, $wpdb;
    if (!empty($wp_query->query_vars['s']) && $this->is_search_key()) {
      $search = $wp_query->query_vars['s'];
      $key = $_GET['key'];
      $status = ($wp_version >= 2.1) ? 'post_type = \'post\' AND post_status = \'publish\'' : 'post_status = \'publish\'';
      $where = " AND $wpdb->postmeta.meta_key = '$key' AND $wpdb->postmeta.meta_value LIKE '%$search%' AND $status ";
    }
    return $where;
  }
  
  function search_custom_template($template) {
    if (is_search() && $this->is_search_key() && file_exists(get_template_directory() . '/' . $this->custom_template_default)) {
      $template = get_template_directory() . '/' . $this->custom_template_default;
    }

    if (!$template) {
      $template = get_query_template('search');
    }
      
    return $template;
  }

  function is_search_key($key='') {
    if (isset($_GET['key'])) {
      if (!empty($_GET['key']) || (!empty($key) && ($key = $_GET['key']))) {
        return true;
      }
    }
    return false;
  }
  
  function render_widget() {
    echo '<li>';
      include(get_template_directory() . '/searchform-transcript.php');
    echo '</li>';
  }
}

?>