<?php

class ComicPress {
  var $comicpress_options = array(
    'comic_category_id' => 1,
    'comic_dimensions' => '760x',
    'rss_dimensions' => '350x',
    'archive_dimensions' => '125x',
    'category_order' => false,
    'blogpost_count' => 10
  );
  var $additional_stylesheets = array();
  
  var $comic_post_attachments_cache = array();
  
  var $category_tree = array();

  function load() {
    $result = get_option('comicpress-options');
    if (is_array($result)) {
      $this->comicpress_options = array_merge($this->comicpress_options, $result);
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
    
    add_action('wp_head', array(&$this, 'wp_head'));
  }

  function wp_head() {
    foreach ($this->additional_stylesheets as $uri) { ?>
      <link rel="stylesheet" href="<?php echo get_template_directory_uri() . $uri ?>" type="text/css" />
    <?php }
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
    $terminal_comic_query->query("showposts=1&order=${sort_order}&cat=${category_id}&status=publish");
    if ($terminal_comic_query->have_posts()) {
      return reset($terminal_comic_query->posts);
    }
    return false;
  }
  
  function get_first_comic() {
    return $this->get_terminal_post_in_category($this->get_all_comic_categories_as_cat_string());
  }
  
  function get_last_comic() {
    return $this->get_terminal_post_in_category($this->get_all_comic_categories_as_cat_string(), false);  
  }
  
  function get_nav_comics() {
    global $post;
    
    $comic_posts = array();
    foreach (array('first', 'last', 'previous', 'next') as $which) {
      $comic_posts[$which] = $this->{"get_${which}_comic"}(); 
    }
    
    $comic_posts['show_first']    = ($comic_posts['first']->ID != $comic_posts['last']->ID);
    $comic_posts['show_previous'] = (!empty($comic_posts['previous']) && ($comic_posts['first']->ID != $comic_posts['previous']->ID));
    $comic_posts['show_next']     = (!empty($comic_posts['next']) && ($comic_posts['last']->ID != $comic_posts['next']->ID));
    $comic_posts['show_last']     = (trim($post->ID) != trim($comic_posts['last']->ID));
    
    return $comic_posts;
  }
  
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
   * Given a category ID or an array of category IDs, create an exclusion string that will
   * filter out every category but the provided ones.
   */
  function get_string_to_exclude_all_but_provided_categories($category) {
    return implode(",", array_diff($category_ids, $category));
  }
  
  function exclude_all_but_provided_categories($category) {
    $category_ids = array_keys($this->get_all_category_objects_by_id());
    if (!is_array($category)) { $category = array($category); }    
  }

  function get_leaves_of_tree($tree) {
    $leaves = array();
    foreach ($tree as $branch) { $leaves[] = end(explode("/", $branch)); }
    return $leaves;
  }

  function _new_wp_query() {
    return new WP_Query();
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