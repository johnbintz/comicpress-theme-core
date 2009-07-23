<?php

class ComicPressAddonCore extends ComicPressAddon {  
  /**
   * Initialize the addon.
   * @param ComicPress $comicpress The master ComicPress object.
   */
  function init($comicpress) {
    add_action('admin_menu', array(&$this, 'setup_admin_interface'));
    add_filter('attachment_fields_to_edit', array(&$this, 'setup_comic_metadata_buttons'), 10, 2);
    add_action('show_comic', array(&$this, 'show_comic'), 1, 1);
    add_action('show_archive', array(&$this, 'show_archive'), 1, 1);
    add_action('show_rss', array(&$this, 'show_rss'), 1, 1);
    add_filter('the_content', array(&$this, 'insert_comic_feed'));

    if (isset($_GET['randomcomic'])) {
      add_action('template_redirect', array(&$this, 'go_to_random_comic'));
    }

    $this->comicpress = $comicpress;
    
    $this->comic_image_types = array(  
      'none'    => __('Not a comic', 'comicpress'),
      'comic'   => __('Comic', 'comicpress'),
      'rss'     => __('RSS', 'comicpress'),
      'archive' => __('Archive', 'comicpress')
    );
  }

  /**
   * Callback to send the reader to a random comic.
   */
  function go_to_random_comic() {
    $random_comic_query = new WP_Query();
    $random_comic_query->query('showposts=1&orderby=rand&cat=' . $this->comicpress->get_all_comic_categories_as_cat_string());
    while ($random_comic_query->have_posts()) {
      $random_comic_query->the_post();
      $random_comic_id = get_the_ID();
      break;
    }
    if (!empty($random_comic_id)) {
      wp_redirect(get_permalink( $random_comic_id ));
    }
  }

  /**
   * Markup to insert a comic into the comic feed.
   */
  function comic_feed() { ?>
    <p><a href="<?php the_permalink() ?>"><?php do_action('show_rss') ?></a></p><?php
  }

  /**
   * Filter to insert the comic into the RSS feed.
   */
  function insert_comic_feed($content) {
    if (is_feed() && in_comic_category()) {
      return $this->comic_feed() . $content;
    } else {
      return $content;
    }
  }

  /**
   * Set up the admin interface and meta boxes.
   */
  function setup_admin_interface() {
    add_theme_page(__("ComicPress Core", 'comicpress'), __('ComicPress Core', 'comicpress'), 'edit_themes', basename(__FILE__), array(&$this, 'render_admin'));
    
    if (isset($_REQUEST['post'])) {
      add_meta_box("comic-image-ordering", __("Comic Image Ordering", 'comicpress'), array(&$this, 'render_comic_image_ordering'), "post", "normal", "low");
    }
  }
  
  /**
   * Show comic media.
   * @param object $override_post If not nul;, the post to use instead of the current Loop post.
   * @param string $method The method to call on the comic post.
   */
  function show_media($override_post, $method) {
    global $post;
    $post_to_use = (is_null($override_post)) ? $this->comicpress->get_last_comic() : $post;
    
    switch ($this->comicpress->comicpress_options['comic_space']) {
      case "comic_only":
        $comic_post = new ComicPressComicPost($post_to_use, &$this->comicpress);
        $comic_post->{$method}();
        break;
      case "post_content":
        $t = $post;
        $post = $post_to_use;
        include_partial('index-blog-post');
        $post = $t;
        break;
    }
  }
  
  /**
   * Show a comic.
   */
  function show_comic($override_post = null) {
    $this->show_media($override_post, "display_comics");
  }
  
  /**
   * Show an archive comic.
   */
  function show_archive($override_post = null) {
    $this->show_media($override_post, "display_archive");
  }
  
  /**
   * Show an RSS comic.
   */
  function show_rss($override_post = null) {
    $this->show_media($override_post, "display_rss");
  }

  /**
   * Modify the Media Gallery for ComicPress use.
   */
  function setup_comic_metadata_buttons($form_fields, $post) {
    global $pagenow;
  
    $current_type = get_post_meta($post->ID, 'comic_image_type', true);
    
    $field_html_lines = array();
    $field_html_lines[] = '<input type="hidden" name="cp[_nonce]" value="' . wp_create_nonce('comicpress')  . '" />';
    foreach ($this->comic_image_types as $field => $label) {
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

    if ($pagenow !== "media.php") {
      $form_fields['auto_attach'] = array(
        'label' => __("Auto-attach?", 'comicpress'),
        'input' => 'html',
        'html' => '<input type="checkbox" name="attachments[' . $post->ID . '][auto_attach]" value="yes" checked="checked" /> <em>'
                . __('Attach to this post w/o needing to insert into post', 'comicpress')
                . '</em>'
      );
    }
    
    return $form_fields;
  }

  /**
   * Render the admin interface.
   */
  function render_admin() {
    $nonce = wp_create_nonce('comicpress');
    $root_categories = $this->get_root_categories();

    include(dirname(__FILE__) . '/partials/options-admin.inc'); 
  }

  /**
   * Render the comic image ordering interface.
   */
  function render_comic_image_ordering() {
    if (isset($_REQUEST['post'])) {
      $comic_post = new ComicPressComicPost($_REQUEST['post'], &$this->comicpress);
      $ordering = $comic_post->normalize_comic_image_ordering();
      if (is_array($ordering)) {
        foreach ($ordering as $type => $attachment_ids) {
          echo '<h3>' . $this->comic_image_types[$type] . '</h3>';
          $index = 1;
          foreach ($attachment_ids as $attachment_id) {
            echo '<img src="' . wp_get_attachment_url($attachment_id) . '" width="60" height="60" />';
          }
          echo '<input type="text" name="cp[ordering][' . $type . '][' . $attachment_id . ']" value="' . $index . '" />';
        }
      } 
    }
  }

  /**
   * Get all categories with a parent ID of 0.
   * @return array All root categories.
   */
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

  /**
   * Create &lt;option&gt; elements for each of the provided categories.
   * @param array $categories The categories to display as either IDs or category objects.
   * @param int $selected_id The category to mark as selected.
   * @return string The category options as HTML.
   */
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
  
  /**
   * Create a dimension selector.
   * @param string $root The field name root.
   * @param $dimension The dimension to pre-fill into the fields.
   * @return string The dimension selector as HTML.
   */
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
  
  /**
   * Determine whether or not storyline categories can be moved or not.
   * @return array The storyline nodes with their move statuses.
   */
  function get_storyline_move_statuses() {
    $nodes_with_statuses = array();
    for ($i = 0, $il = count($this->comicpress->category_tree); $i < $il; ++$i) {
      $node = $this->comicpress->category_tree[$i];
      $nodes_with_statuses[$node] = array();
      $parts_count = count(explode("/", $node));
      foreach (array(
        '0' => -1,
        '1' => 1
      ) as $position => $direction) {
        $current_node_index = $i;
        $status = false;
        do {
          $current_node_index += $direction;
          if (isset($this->comicpress->category_tree[$current_node_index])) {
            $current_node = $this->comicpress->category_tree[$current_node_index];
            $current_parts_count = count(explode("/", $current_node));
            if ($current_parts_count == $parts_count) { $status = true; break; }
            if ($current_parts_count < $parts_count) { break; }
          }
        } while (isset($this->comicpress->category_tree[$current_node_index]));
        $nodes_with_statuses[$node][$position] = $status;
      }
    }
    return $nodes_with_statuses;
  }

  /**
   * Move a category in the hierarchy.
   * @param array $category The category to move.
   * @param int $direction The direction to move it in (-1 for up, 1 for down)
   */
  function move_storyline_category_order($category, $direction) {
    for ($i = 0, $il = count($this->comicpress->category_tree); $i < $il; ++$i) {
      $node = $this->comicpress->category_tree[$i];
      $parts = explode("/", $node);
      $parts_count = count($parts);
      
      if (end($parts) == $category) {
        $current_node_index = $i;
        $target_index = null;
        do {
          $current_node_index += $direction;
          
          if (isset($this->comicpress->category_tree[$current_node_index])) {
            $current_parts_count = count(explode("/", $this->comicpress->category_tree[$current_node_index]));
            if ($current_parts_count == $parts_count) { $target_index = $current_node_index; break; }
            if ($current_parts_count < $parts_count) { break; }
          }
        } while (isset($this->comicpress->category_tree[$current_node_index]));
        
        if (!is_null($target_index)) {
          $end_i_index = $i;
          do {
            $end_i_index++;
            if (isset($this->comicpress->category_tree[$end_i_index])) {
              if (strpos($this->comicpress->category_tree[$end_i_index], $node) !== 0) {
                break;
              }
            }
          } while (isset($this->comicpress->category_tree[$end_i_index]));
        
          $new_order = $this->comicpress->category_tree;
          $target_index_node = $new_order[$target_index];

          $move = array_splice($new_order, $i, $end_i_index - $i);
          $target = array_search($target_index_node, $new_order);
          $prefix = array_splice($new_order, 0, $target);
          
          if ($direction == -1) {
            $new_order = array_merge($prefix, $move, $new_order);
          } else {
            $leftover = array();
            if (count($new_order) > 1) {
              $start_parts_count = count(explode("/", $new_order[0]));
              for ($j = 1, $jl = count($new_order); $j < $jl; ++$j) {
                if (count(explode("/", $new_order[$i])) < $start_parts_count) {
                  $leftover = array_splice($new_order, $j); break;
                }
              }
            }
            
            $new_order = array_merge($prefix, $new_order, $move, $leftover);
          }

          $this->comicpress->comicpress_options['category_order'] = $new_order;
          
          break;
        }
      }
    }
  }
  
  /**
   * Update attachment information.
   */
  function handle_update_attachments() {
    foreach ($_POST['attachments'] as $post_id => $settings) {
      if (isset($settings['comic_image_type'])) {
        update_post_meta($post_id, 'comic_image_type', $settings['comic_image_type']); 
      }
      if (isset($settings['auto_attach']) && isset($settings['post_parent'])) {
        $media_post = get_post($post_id);
        $media_post->post_parent = $settings['post_parent'];
        wp_update_post($media_post);
      }
    }
  }
  
  /**
   * Update ComicPress options.
   */
  function handle_update_comicpress_options() {
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
          case 'mini_dimensions':
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
          case 'comic_space':
          case 'category_page_usage':
            $this->comicpress->comicpress_options[$option] = $_POST['cp'][$option];
            break;
        }
      }
    }
  }
  
  /**
   * Handle an update.
   */
  function handle_update() {
    if (isset($_POST['attachments'])) {
      //coming from media editor
      $this->handle_update_attachments();
    } else {
      //coming from us
      $this->handle_update_comicpress_options();
      
      if (isset($_GET['cp']['move_direction']) && isset($_GET['cp']['category'])) {
        $this->move_storyline_category_order($_GET['cp']['category'], $_GET['cp']['move_direction']);
      }

      $this->comicpress->save();
      
      $this->info("ComicPress configuration updated.");
      
      $this->comicpress->init();
    }
  }
  
  function _glob($pattern) { return glob($pattern); }
  function _file_get_contents($file) { return file_get_contents($file); }
  
  function get_layout_choices() {
    $layouts = array();
    foreach ($this->_glob(get_template_directory() . '/layouts/*') as $file) {
      $content = $this->_file_get_contents($file); 
      if (preg_match('#/\*.*Layout Name: ([^\n]+).*\*/#s', $content, $matches) > 0) {        
        $layouts[pathinfo($file, PATHINFO_BASENAME)] = $matches[1];
      }
    }
    return $layouts;
  }
}

?>