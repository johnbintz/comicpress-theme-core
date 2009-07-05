<?php

class ComicPressOptionsAdmin {
  var $comicpress_options = array(
    'comic_category_id' => 1,
    'thumbnail_dimensions' => '760x'
  );

  function render_admin() {
    $nonce = wp_create_nonce('comicpress');
    $root_categories = $this->get_root_categories();

    $this->get_comicpress_options();

    include(dirname(__FILE__) . '/partials/options-admin.inc');	
  }

  function get_root_categories() {
    $root_categories = array();
    foreach (get_all_category_ids() as $id) {
      $category = get_category($id);
      if (!empty($category)) {
        if ($category->parent == 0) {
          $root_categories[] = $category;
        }
      }
    }
    return $root_categories;
  }

  function create_category_options($categories, $selected_id) {
    $output = array();
    if (is_array($categories)) {
      $final_categories = array();
      foreach ($categories as $category) {
        if (is_numeric($category)) {
          $result = get_category($category);
          if (!is_a($result, "WP_Error")) {
            $final_categories[] = $result;
          }
        }
        if (is_object($category)) {
          $final_categories[] = $category;
        }
      }

      foreach ($final_categories as $category) {
        $output[] = '<option value="' . $category->term_id . '"' . ($category->term_id == $selected_id ? ' selected="selected"' : '') . '>' . $category->name . '</option>';
      }
    }
    return implode("\n", $output);
  }
  
  function create_dimension_selector($root, $dimension) {
    $output = array();
    
    $parts = explode("x", $dimension);
    foreach (array(
      'width' => __('Width', 'comicpress'),
      'height' => __('Height', 'comicpress')
    ) as $id => $name) {
      $dim = array_shift($parts);
      if (!empty($dim) && !is_numeric($dim)) { $dim = ""; }
      $output[] = '<label>' . $name . ': <input type="text" name="' . $root . '[' . $id . ']" value="' . $dim . '" /></label>';       
    }
    return implode("\n", $output);
  }
  
  function get_comicpress_options() {
    $result = get_option('comicpress-options');
    if (is_array($result)) {
      $this->comicpress_options = $result;
    }
  }
  
  function update_comicpress_options() {
    if (is_array($this->comicpress_options)) {
      update_option('comicpress-options', $this->comicpress_options);
    }
  }
}

$comicpress_options_admin = new ComicPressOptionsAdmin();

function __comicpress_add_options_admin() {
  global $comicpress_options_admin;
  add_theme_page(__("ComicPress Options", 'comicpress'), __('ComicPress Options', 'comicpress'), 'edit_themes', basename(__FILE__), array($comicpress_options_admin, 'render_admin'));
}

?>
