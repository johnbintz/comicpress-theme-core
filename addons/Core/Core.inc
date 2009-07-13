<?php

class ComicPressAddonCore extends ComicPressAddon {
  function init($comicpress) {
    add_action('admin_init', array(&$this, 'setup_admin_interface'));
    add_filter('attachment_fields_to_edit', array(&$this, 'setup_comic_metadata_buttons'), 10, 2);
    add_action('show_comic', array(&$this, 'show_comic'), 1, 1);
    add_action('show_archive', array(&$this, 'show_archive'), 1, 1);
    add_action('show_rss', array(&$this, 'show_rss'), 1, 1);
    add_filter('the_content', array(&$this, 'insert_comic_feed'));

    $this->comicpress = $comicpress;
  }

  function comic_feed() { ?>
    <p><a href="<?php the_permalink() ?>"><?php do_action('show_rss') ?></a></p><?php
  }

  function insert_comic_feed($content) {
    if (is_feed() && in_comic_category()) {
      return $this->comic_feed() . $content;
    } else {
      return $content;
    }
  }


  function setup_admin_interface() {
    add_theme_page(__("ComicPress Core", 'comicpress'), __('ComicPress Core', 'comicpress'), 'edit_themes', basename(__FILE__), array(&$this, 'render_admin'));
    
    if (isset($_REQUEST['post'])) {
      add_meta_box("comic-image-ordering", __("Comic Image Ordering", 'comicpress'), array(&$this, 'render_comic_image_ordering'), "post", "normal", "low");
    }
  }
  
  function show_media($override_post, $method) {
    global $post;
    $post_to_use = (is_null($override_post)) ? $this->comicpress->get_last_comic() : $post;
    
    $comic_post = new ComicPressComicPost($post_to_use, &$this->comicpress);
    $comic_post->{$method}();
  }
  
  function show_comic($override_post = null) {
    $this->show_media($override_post, "display_comics");
  }
  
  function show_archive($override_post = null) {
    $this->show_media($override_post, "display_archive");
  }
  
  function show_rss($override_post = null) {
    $this->show_media($override_post, "display_rss");
  }

  function setup_comic_metadata_buttons($form_fields, $post) {
    $comic_image_types = array(
      'none' => __('Not a comic', 'comicpress'),
      'comic' => __('Comic', 'comicpress'),
      'rss' => __('RSS', 'comicpress'),
      'archive' => __('Archive', 'comicpress'),
    );
    
    $current_type = get_post_meta($post->ID, 'comic_image_type', true);
    
    $field_html_lines = array();
    $field_html_lines[] = '<input type="hidden" name="cp[_nonce]" value="' . wp_create_nonce('comicpress')  . '" />';
    foreach ($comic_image_types as $field => $label) {
      $field_html_lines[] = '<label>'
                          . ' <input type="radio" name="attachments['
                          . $post->ID
                          . '][comic_image_type]" value="'
                          . $field
                          . '"'
                          . (($field == $current_type) ? ' checked="checked"' :  '')
                          . ' /> '
                          . $label
                          . '</label>';
    }
    
    $form_fields['comic_image_type'] = array(
      'label' => __("Comic Image Type", 'comicpress'),
      'input' => 'html',
      'html' => '<center>' . implode("\n", $field_html_lines) . '</center>'
    );
    
    $form_fields['auto_attach'] = array(
      'label' => __("Auto-attach?", 'comicpress'),
      'input' => 'html',
      'html' => '<input type="checkbox" name="attachments[' . $post->ID . '][auto_attach]" value="yes" checked="checked" /> <em>'
              . __('Attach to this post w/o needing to insert into post', 'comicpress')
              . '</em>'
    );
    
    return $form_fields;
  }

  function render_admin() {
    $nonce = wp_create_nonce('comicpress');
    $root_categories = $this->get_root_categories();

    include(dirname(__FILE__) . '/partials/options-admin.inc'); 
  }

  function render_comic_image_ordering() {
    echo "made it";
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
        $output[] = '<option value="' . $category->term_id . '"' . (($category->term_id == $selected_id) ? ' selected="selected"' : '') . '>' . $category->name . '</option>';
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
  
  function handle_update() {
    if (isset($_POST['attachments'])) {
      //coming from media editor
      foreach ($_POST['attachments'] as $post_id => $settings) {
        if (isset($settings['comic_image_type'])) {
          update_post_meta($post_id, 'comic_image_type', $settings['comic_image_type']); 
        }
        if (isset($settings['auto_attach'])) {
          if (isset($settings['post_parent'])) {
            $media_post = get_post($post_id);
            $media_post->post_parent = $settings['post_parent'];
            wp_update_post($media_post);
          }
        }
      }
    } else {
      //coming from us
      foreach ($this->comicpress->comicpress_options as $option => $value) {
        if (isset($_POST['cp'][$option])) {
          switch ($option) {
            case 'comic_category_id':
              if (is_numeric($_POST['cp'][$option])) {
                $result = get_category($_POST['cp'][$option]);
                if (!(is_a($result, 'WP_Error') || empty($result))) {
                  $this->comicpress->comicpress_options[$option] = $_POST['cp'][$option]; 
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
                  $this->comicpress->comicpress_options[$option] = implode("x", $dim_parts);
                }
              }
              break;
            case 'blogpost_count':
              $this->comicpress->comicpress_options[$option] = (int)$_POST['cp'][$option];
              break;
          }
        }
      }
      $this->comicpress->save();
      
      $this->info("ComicPress configuration updated.");
    }
  }
}

?>