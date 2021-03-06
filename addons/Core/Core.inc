<?php

class ComicPressAddonCore extends ComicPressAddon { 
  var $is_addon_manager = true;
  var $cannot_be_disabled = true;
  var $name = "ComicPress Core";
   
  /**
   * Initialize the addon.
   * @param ComicPress $comicpress The master ComicPress object.
   */
  function init() {
		$this->comicpress = &ComicPress::get_instance();
		
		add_action('admin_menu', array(&$this, 'setup_admin_interface'));
    add_filter('attachment_fields_to_edit', array(&$this, 'setup_comic_metadata_buttons'), 10, 2);
    add_action('show_comic', array(&$this, 'show_comic'), 1, 1);
    add_action('show_archive', array(&$this, 'show_archive'), 1, 1);
    add_action('show_rss', array(&$this, 'show_rss'), 1, 1);
    add_filter('the_content', array(&$this, 'insert_comic_feed'));

    if (isset($_GET['randomcomic'])) {
      add_action('template_redirect', array(&$this, 'go_to_random_comic'));
    }

    if (current_user_can('edit_posts') && isset($comicpress->comicpress_options['helpers']['show_inline_comic_ordering'])) {
      add_filter('comicpress_attached_image', array(&$this, 'comicpress_attached_image'), 10, 3);
      add_filter('comicpress_display_attached_images', array(&$this, 'comicpress_display_attached_images'), 10, 2);
    }

    if (isset($this->comicpress->comicpress_options['helpers']['show_partials_info'])) {
      wp_enqueue_script('prototype');
      add_filter('comicpress_partial', array(&$this, 'show_inline_comicpress_partial_editor'), 20, 2);
      add_action('wp_head', array(&$this, 'add_inline_comicpress_partial_edit_hooks'));
      add_action('wp_footer', array(&$this, 'add_inline_comicpress_partial_holders'));
    }

    if (is_admin()) {
      add_action('admin_head', array(&$this, 'add_edit_area'));
    } else {
      add_action('wp_head', array(&$this, 'add_edit_area'));
    }
    
    $this->comic_image_types = array(  
      'none'    => __('Not a comic', 'comicpress'),
      'comic'   => __('Comic', 'comicpress'),
      'rss'     => __('RSS', 'comicpress'),
      'archive' => __('Archive', 'comicpress')
    );
  }

  function add_edit_area() { ?>
    <script type="text/javascript" src="<?php echo get_template_directory_uri() . str_replace(realpath(get_template_directory()), '', realpath(dirname(__FILE__) . '/edit_area/edit_area_full.js')) ?>"></script>
  <?php }

  function show_inline_comicpress_partial_editor($content, $target) {
    $target = str_replace(get_template_directory() . DIRECTORY_SEPARATOR, '', $target);
    $content = preg_replace('#(<div class="partial-helper">)([^\<]+)(</div>)#', '\1\2 | <a href="#" rel="edit-' . md5($target) . '">Edit</a>\3', $content);
    return $content;
  }

  function add_inline_comicpress_partial_edit_hooks() { ?>
    <style type="text/css">
      .inline-partial-editor {
        display: none;
        background: white;
        border: solid #333 1px;
        padding: 5px;
        z-index: 200
      }
    </style>
    <script type="text/javascript">
      Event.observe(window, 'load', function() {
        $$('.partial-helper a').each(function(a) {
          Event.observe(a, 'click', function(e) {
            Event.stop(e);
            var edit_form = $(a.rel);
            if (edit_form) {
              if (!edit_form.visible()) {
                var holder = $(a.parentNode);
                var holder_dim = holder.getDimensions();
                edit_form.style.display = "block";
                edit_form.style.position = "absolute";
                edit_form.clonePosition(holder, { setWidth: false, setHeight: false, offsetTop: holder_dim['height'] });

                editAreaLoader.init({
                  id: "text-" + a.rel,
                  syntax: "php",
                  start_highlight: true
                });
              }
            }
          });
        });
      });
    </script>
  <?php }

  function add_inline_comicpress_partial_holders() {
    $nonce = wp_create_nonce('comicpress');

    $valid_partials = array();
    foreach (glob(get_template_directory() . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . '*.inc') as $partial) {
      if (preg_match('#(partials.*)\.inc$#', $partial, $matches) > 0) {
        $valid_partials[] = $matches[1];
      }
    }

    foreach ($valid_partials as $key) {
      if (isset($this->comicpress->comicpress_options['override_partials'][$key])) {
        $partial_code = htmlentities($this->comicpress->comicpress_options['override_partials'][$key]);
      } else {
        $partial_code = htmlentities(file_get_contents(get_template_directory() . DIRECTORY_SEPARATOR . $key . '.inc'));
      }

      $hash = md5($key); ?>
        <form id="edit-<?php echo $hash ?>" method="post" class="inline-partial-editor" style="display: none">
          <input type="hidden" name="cp[_nonce]" value="<?php echo $nonce ?>" />
          <textarea id="text-edit-<?php echo $hash ?>" name="cp[code]" cols="80" rows="15" wrap="off"><?php echo $partial_code ?></textarea>
          <input type="hidden" name="cp[partial]" value="<?php echo $key ?>" /><br />
          <input type="submit" class="button" name="cp[action]" value="<?php _e('Update partial', 'comicpress') ?>" />
        </form>
      <?php
    }
  }

  function comicpress_attached_image($content, $attachment_id, $index) {
    $content .= '<label class="comic-image-ordering">'
              . __('Image index:', 'comicpress')
              . ' '
              . '<input type="text" name="cp[ordering][comic]['
              . $attachment_id
              . ']" value="'
              . $index
              . '" /></label>';
              
    return $content;
  }
  
  function comicpress_display_attached_images($content, $post_id) {
    $content = '<form method="post">'
             . '<input type="hidden" name="cp[_nonce]" value="'
             . wp_create_nonce('comicpress')
             . '" />'
             . '<input type="hidden" name="post_ID" value="'
             . $post_id
             . '" />'
             . $content
             . '<input type="submit" value="'
             . __('Change image ordering', 'comicpress')
             . '" />'
             . '</form>';
    
    return $content;
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
		global $plugin_page;

    add_theme_page(__("ComicPress Core", 'comicpress'), __('ComicPress Core', 'comicpress'), 'edit_themes', 'comicpress/render_admin', array(&$this, 'render_admin'));
    add_theme_page(__("Edit Partials", 'comicpress'), __('Edit Partials', 'comicpress'), 'edit_themes', 'comicpress/edit_partials', array(&$this, 'render_edit_partials'));
    
    if (isset($_REQUEST['post'])) {
      add_meta_box("comic-image-ordering", __("Comic Image Ordering", 'comicpress'), array(&$this, 'render_comic_image_ordering'), "post", "normal", "low");
    }
		
		if ($plugin_page == 'comicpress/render_admin') {
			wp_enqueue_style('cp-admin', get_stylesheet_directory_uri() . '/css/cp-admin.css');
			wp_enqueue_script('cp-admin', get_stylesheet_directory_uri() . '/js/Storyline.js', array('jquery', 'jquery-ui-sortable'));
		}
  }
  
  /**
   * Show comic media.
   * @param object $override_post If not nul;, the post to use instead of the current Loop post.
   * @param string $method The method to call on the comic post.
   */
  function show_media($override_post, $method, $format) {
    global $post;
    $post_to_use = $post;
    
    switch ($this->comicpress->comicpress_options['comic_space']) {
      case "comic_only":
        $comic_post = new ComicPressComicPost($post_to_use, &$this->comicpress);
        $comic_post->{$method}($format);
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
  function show_comic($override_post = null, $format = "%s<br />") {
    $this->show_media($override_post, "display_comics", $format);
  }
  
  /**
   * Show an archive comic.
   */
  function show_archive($override_post = null, $format = "%s<br />") {
    $this->show_media($override_post, "display_archive", $format);
  }
  
  /**
   * Show an RSS comic.
   */
  function show_rss($override_post = null, $format = "%s<br />") {
    $this->show_media($override_post, "display_rss", $format);
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
		$storyline = new ComicPressStoryline();
		$storyline->normalize();
		$storyline->read_from_options();

    include(dirname(__FILE__) . '/partials/options-admin.inc'); 
  }
	
	function _render_admin_storyline_tree($node, $parent_id = "0") {
	  foreach ($node as $category_id => $children) {
			$category = get_category($category_id);
			echo '<div class="cp-category-info category-' . $parent_id . '/' . $category_id . '">';
				echo '<span>' . $category->name . '</span>';
				if (is_array($children)) {
					echo '<div class="cp-children">';
						$this->_render_admin_storyline_tree($children, $parent_id . '/' . $category_id);
					echo '</div>';
				}
			echo '</div>';
		}
	}
  
  function render_edit_partials() {
    $nonce = wp_create_nonce('comicpress');

    include(dirname(__FILE__) . '/partials/edit-partials.inc');
  }

  /**
   * Render the comic image ordering interface.
   */
  function render_comic_image_ordering() {
    if (isset($_REQUEST['post'])) {
      $comic_post = new ComicPressComicPost(get_post($_REQUEST['post']), &$this->comicpress);
      $ordering = $comic_post->normalize_comic_image_ordering();
      echo '<input type="hidden" name="cp[_nonce]" value="' . wp_create_nonce('comicpress')  . '" />';
      if (is_array($ordering)) {
        foreach ($ordering as $type => $attachment_ids) {
          echo '<h3>' . $this->comic_image_types[$type] . '</h3>';
          $index = 1;
          foreach ($attachment_ids as $attachment_id) {
            echo '<img src="' . wp_get_attachment_url($attachment_id) . '" width="60" height="60" />';
            echo '<input size="3" type="text" name="cp[ordering][' . $type . '][' . $attachment_id . ']" value="' . $index . '" />';
            ++$index;
          }
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
  function handle_update_comicpress_options($info) {
    foreach (array('helpers', 'options') as $type) {
      $this->comicpress->comicpress_options[$type] = array();
    }
    foreach ($this->comicpress->comicpress_options as $option => $value) {
      if (isset($info[$option])) {
        switch ($option) {
          case 'comic_category_id':
            if (is_numeric($info[$option])) {
              $result = get_category($info[$option]);
              if (!(is_a($result, 'WP_Error') || empty($result))) {
                $this->comicpress->comicpress_options[$option] = $info[$option]; 
              } 
            }
            break;
          case 'comic_dimensions':
          case 'rss_dimensions':
          case 'archive_dimensions':
          case 'mini_dimensions':
            if (is_array($info[$option])) {
              $dim_parts = array();
              $is_valid = true;
              foreach (array('width', 'height') as $field) {
                $requested_dim = trim($info[$option][$field]);
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
            $this->comicpress->comicpress_options[$option] = (int)$info[$option];
            break;
          case 'comic_space':
          case 'category_usage':
          case 'layout';
            $this->comicpress->comicpress_options[$option] = $info[$option];
            break;
          case 'helpers':
          case 'addons':
            foreach ($info[$option] as $type => $set) {
              $this->comicpress->comicpress_options[$option][$type] = true;
            }
            break;
					case 'storyline_order':
						$storyline = new ComicPressStoryline();
						$storyline->normalize($info[$option]);
						break;
        }
      }
    }
  }
  
  function handle_update_comic_ordering() {
    if (is_numeric($_POST['post_ID'])) {
      if ($post = get_post($_POST['post_ID'])) {
        $comic_post = new ComicPressComicPost(&$post);
        $comic_post->change_comic_image_ordering($_POST['cp']['ordering']);
        
        if (isset($this->comicpress->comicpress_options['helpers']['show_inline_comic_ordering']) && !is_admin()) {
          $this->info(sprintf(__("Comic ordering for post #%s updated", 'comicpress'), $_POST['post_ID']));
        }
      }
    }
  }

  function handle_update_override_partial($info) {
    switch ($info['action']) {
      case __('Update partial', 'comicpress'):
        $this->comicpress->comicpress_options['override_partials'][$info['partial']] = stripslashes($info['code']);
        break;
      case __('Delete override partial', 'comicpress'):
        unset($this->comicpress->comicpress_options['override_partials'][$info['partial']]);
        break;
    }
  }

  /**
   * Handle an update.
   */
  function handle_update($info) {
    if (isset($_POST['attachments'])) {
      //coming from media editor
      $this->handle_update_attachments();
    } else if (is_array($info['ordering'])) {
      // comic ordering
      
			/*
      if (isset($_POST['meta'])) {
        $meta_key_to_ignore = false;
        foreach ($_POST['meta'] as $meta_key => $params) {
          foreach ($params as $type => $value) {
            if ($type == "key" && $value == "comic_ordering") {
              $meta_key_to_ignore = $meta_key; break;
            }
          }
        }
        
        if ($meta_key_to_ignore !== false) {
          unset($_POST['meta'][$meta_key_to_ignore]);
        }
      }
      
      $this->handle_update_comic_ordering($info);
			*/
    } else if (isset($info['partial'])) {
      $this->handle_update_override_partial($info);
      $this->info(sprintf(__("Partial %s updated.", 'comicpress'), $info['partial']));

      $this->comicpress->save();
      $this->comicpress->init();
    } else {
      //coming from us
      $this->handle_update_comicpress_options($info);
      
      $this->comicpress->save();
      
      $this->info(__("ComicPress configuration updated.", 'comicpress'));
      
      $this->comicpress->init();
    }
  }
  
  /**
   * Create the dropdown for choosing a layout.
   */
  function create_layout_options($layouts, $current_layout) {
    $output = array();
    foreach ($layouts as $layout_filename => $info) {
      $output[] = '<option value="' . $layout_filename . '"' . (($layout_filename == $current_layout) ? ' selected="selected"' : '') . '>' . $info['Layout Name'] . '</option>';
    }
    return implode("\n", $output);
  }
}

?>
