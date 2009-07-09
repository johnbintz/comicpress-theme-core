<?php

class ComicPress {
  var $comicpress_options = array(
    'comic_category_id' => 1,
    'comic_dimensions' => '760x',
    'rss_dimensions' => '350x',
    'archive_dimensions' => '125x',
    'category_order' => false
  );
  
  var $category_tree = array();

  function load() {
    $result = get_option('comicpress-options');
    if (is_array($result)) {
      $this->comicpress_options = $result;
    }
  }
  
  function save() {
    if (is_array($this->comicpress_options)) {
      update_option('comicpress-options', $this->comicpress_options);
    }
  }

  function init() {
    $this->load();
    $this->get_all_category_objects_by_id();
    $this->flatten_categories();
    $this->separate_categories();
    $this->sort_comic_categories();
  }
  
  function get_comic_img_tag($url, $type, $additional_parameters = array()) {
    $dimensions = array();
    if (isset($this->comicpress_options["${type}_dimensions"])) {
      list($width, $height) = explode("x", $this->comicpress_options["${type}_dimensions"]);
      $dimensions = compact('width', 'height');
    }
    
    $output = '<img src="' . $url . '" ';
    foreach (array('width', 'height') as $field) {
      if (!empty($dimensions[$field])) {
        $output .= $field . '="' . $dimensions[$field] . '" ';
      }
    }
    if (is_array($additional_parameters)) {
      foreach ($additional_parameters as $parameter => $value) {
        $output .= $parameter . '="' . $value . '" ';    
      }
    }
    $output .= "/>";
    
    return $output;
  }
  
  /**
   * Flatten all WP categories into nodes like 0/3/5.
   */
  function flatten_categories() {
    $this->category_tree = array();

    foreach (array_keys($this->categories_by_id) as $category_id) {
      $this->category_tree[] = $this->categories_by_id[$category_id]->parent . '/' . $category_id;
    }

    do {
      $all_ok = true;
      for ($i = 0; $i < count($this->category_tree); ++$i) {
        $current_parts = explode("/", $this->category_tree[$i]);
        if (reset($current_parts) != 0) {

          $all_ok = false;
          for ($j = 0; $j < count($this->category_tree); ++$j) {
            $j_parts = explode("/", $this->category_tree[$j]);

            if (end($j_parts) == reset($current_parts)) {
              $this->category_tree[$i] = implode("/", array_merge($j_parts, array_slice($current_parts, 1)));
              break;
            }
          }
        }
      }
    } while (!$all_ok);
    
    return $this->category_tree;
  }
  
  /**
   * Separate categories into comics and non-comics categories.
   */
  function separate_categories() {
    $comic_categories = array();
    $non_comic_categories = array();
    
    foreach ($this->category_tree as $node) {
      $parts = split("/", $node);
      if ($parts[1] == $this->comicpress_options['comic_category_id']) {
        $comic_categories[] = $node;
      } else {
        $non_comic_categories[] = $node;
      }
    }
    
    $this->category_tree = $comic_categories;
    $this->non_comic_categories = $non_comic_categories;
  }
  
  /**
   * Sort the category tree, adding in new categories in the order as necessary.
   */
  function sort_comic_categories() {
    if (is_array($this->comicpress_options['category_order'])) {
      $new_order = array();
      foreach ($this->comicpress_options['category_order'] as $node) {
        if (in_array($node, $this->category_tree)) {
          $new_order[] = $node;
        }
      }
      
      foreach ($this->category_tree as $node) {
        if (!in_array($node, $this->comicpress_options['category_order'])) {
          $new_order[] = $node;
        }
      }
      
      $this->category_tree = $new_order;;
    }
    return $this->category_tree;
  }

  /**
   * Turn the list of categories into a hash table of category objects.
   */
  function get_all_category_objects_by_id() {
    if (empty($this->categories_by_id)) {
      $this->categories_by_id = array();
      foreach (get_categories("hide_empty=0") as $category_object) {
        $this->categories_by_id[$category_object->term_id] = $category_object;
      }
    }
    return $this->categories_by_id;
  }
  
  /**
   * Turn the tree of comics categories into a string to be fed into wp_query functions.
   */
  function get_all_comic_categories_as_cat_string() {
    if (empty($this->all_comic_categories_as_string)) {
      $categories = array();
      foreach ($this->category_tree as $node) {
        $categories[] = end(explode("/", $node));
      }
      $this->all_comic_categories_as_string = implode(",", $categories);
    }
    return $this->all_comic_categories_as_string;
  }

  /**
   * Return true if the current post is in the comics category or a child category.
   */
  function in_comic_category($post_id) {
    $categories = wp_get_post_categories($post_id);
    if (is_array($categories)) {
      foreach ($this->category_tree as $node) {
        if (in_array(end(explode("/", $node)), $categories)) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Find the terminal post in a specific category.
   */
  function get_terminal_post_in_category($category_id, $first = true) {
    $sort_order = $first ? "asc" : "desc";
    $terminal_comic_query =$this->_new_wp_query();
    $terminal_comic_query->query("showposts=1&order=${sort_order}&cat=${category_id}");
    if ($terminal_comic_query->have_posts()) {
      return reset($terminal_comic_query->posts);
    }
    return false;
  }
  
  function _new_wp_query() {
    return new WP_Query();
  }
}

?>