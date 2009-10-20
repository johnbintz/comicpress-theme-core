<?php

class ComicPressDBInterface {
  var $_non_comic_categories, $_all_categories;

  function ComicPressDBInterface() {}
  
  function get_instance() {
    static $instance;
    
    if (!isset($instance)) {
      $instance = new ComicPressDBInterface();
    }
    return $instance;
  }
  
  function set_comic_categories($categories) {
    $this->_non_comic_categories = array();
    $this->_all_categories       = array();
    foreach (get_categories("hide_empty=0") as $category_object) {
      $this->_all_categories[] = $category_object->term_id;
      if (!in_array($category_object->term_id, $categories
      $this->categories_by_id[$category_object->term_id] = $category_object;
    }
    
    $this->_categories = $categories;
  }
  
  /**
   * Find the terminal post in a specific category.
   */
  function get_terminal_post_in_category($category_id, $first = true) {
    $sort_order = $first ? "asc" : "desc";
    $terminal_comic_query = new WP_Query();
    $terminal_comic_query->query("showposts=1&order=${sort_order}&cat=${category_id}&status=publish");
    if ($terminal_comic_query->have_posts()) {
      return reset($terminal_comic_query->posts);
    }
    return false;
  }
  
  /**
   * Get the first comic in a category.
   */
  function get_first_comic($category_id) {
    return $this->get_terminal_post_in_category($category_id);
  }
  
  /**
   * Get the last comic in a category.
   */
  function get_last_comic($category_id) {
    return $this->get_terminal_post_in_category($category_id, false);  
  }

  /**
   * Get the comic post adjacent to the current comic.
   */
  function get_adjacent_comic($category, $next = false, $override_post = null) {
    global $wp_query, $post;
    $temp = $wp_query->is_single;
    $wp_query->is_single = true;
    
    if (!is_null($override_post)) {
      $temp_post = $post;
      $post = $override_post; 
    }
    
    $categories_to_exclude = $this->get_leaves_of_tree($this->non_comic_categories);
    if (!is_null($category)) {
      $categories_to_exclude = $this->exclude_all_but_provided_categories($category);
    }
    
    $result = get_adjacent_post(false, implode(" and ", $categories_to_exclude), !$next);

    $wp_query->is_single = $temp;
    
    if (!is_null($override_post)) {
      $post = $temp_post; 
    }

    return empty($result) ? false : $result;
  }
  
  /**
   * Get the previous comic from the current one.
   */
  function get_previous_comic($category = null, $override_post = null) { return $this->get_adjacent_comic($category, false, $override_post); }

  /**
   * Get the next comic from the current one.
   */
  function get_next_comic($category = null, $override_post = null) { return $this->get_adjacent_comic($category, true, $override_post); }
  
}

?>