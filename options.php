<?php

class ComicPressOptionsAdmin {
  var $comicpress_options = array(
    'comic_category_id' => 1,
    'comic_dimensions' => '760x',
    'rss_dimensions' => '350x',
    'archive_dimensions' => '125x'
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
          if (!(is_a($result, "WP_Error") || empty($result))) {
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
      $output[] = '<label>' . $name . ': <input type="text" name="' . $root . '[' . $id . ']" value="' . $dim . '" size="4" />px</label><br />';       
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

  function handle_update() {
    if (isset($_POST['cp'])) {
      $this->get_comicpress_options();
      foreach ($this->comicpress_options as $option => $value) {
        if (isset($_POST['cp'][$option])) {
          switch ($option) {
            case 'comic_category_id':
              if (is_numeric($_POST['cp'][$option])) {
                $result = get_category($_POST['cp'][$option]);
                if (!(is_a($result, 'WP_Error') || empty($result))) {
                  $this->comicpress_options[$option] = $_POST['cp'][$option]; 
                } 
              }
              break;
            case 'comic_dimensions':
            case 'rss_dimensions':
            case 'archive_dimensions':
              if (is_array($_POST['cp'][$option])) {
                $dim_parts = array();
                $is_valid = true;
                foreach (array('width', 'height') as $field) {
                  $requested_dim = trim($_POST['cp'][$option][$field]);
                  if ($requested_dim == "") {
                    $dim_parts[] = $requested_dim;
                  } else {
                    if ((int)$requested_dim == $requested_dim) {
                      $dim_parts[] = $requested_dim;
                    } else {
                      $is_valid = false; break;
                    }
                  }
                }
                
                if ($is_valid) {
                  $this->comicpress_options[$option] = implode("x", $dim_parts);
                }
              }
              break;
          }
        }
      }
      $this->update_comicpress_options();
    }
  }
  
  function init() {
    $this->get_comicpress_options();  
  }
}

$comicpress_options_admin = new ComicPressOptionsAdmin();

add_action('init', array(&$comicpress_options_admin, 'init'));

function __comicpress_add_options_admin() {
  global $comicpress_options_admin;
  add_theme_page(__("ComicPress Options", 'comicpress'), __('ComicPress Options', 'comicpress'), 'edit_themes', basename(__FILE__), array($comicpress_options_admin, 'render_admin'));

  if (isset($_POST['cp']['_nonce'])) {
    if (wp_verify_nonce($_POST['cp']['_nonce'], 'comicpress')) {
      $comicpress_options_admin->handle_update();
    }
  }
}

?>
