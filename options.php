<?php

class ComicPressOptionsAdmin {
  function render_admin() {
    $nonce = wp_create_nonce('comicpress');

    echo '<div class="wrap">';
      echo '<h2>ComicPress Config</h2>';
      echo '<form method="post">';
        echo '<input type="hidden" name="cp[_nonce]" value="' . $nonce . '" />';
      echo '</form>';
    echo '</div>';
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
}

$comicpress_options_admin = new ComicPressOptionsAdmin();

function __comicpress_add_options_admin() {
  global $comicpress_options_admin;
  add_theme_page(__("ComicPress Options", 'comicpress'), __('ComicPress Options', 'comicpress'), 'edit_themes', basename(__FILE__), array($comicpress_options_admin, 'render_admin'));
}

?>
