<?php

class ComicPressNavigation {
  function init($post, $storyline) {
    $this->post      = $post;
    $this->storyline = $storyline;
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
  function get_first_in_category($category_id) {
    return $this->get_terminal_post_in_category($category_id);
  }
  
  /**
   * Get the last comic in a category.
   */
  function get_last_in_category($category_id) {
    return $this->get_terminal_post_in_category($category_id, false);  
  }
}

?>