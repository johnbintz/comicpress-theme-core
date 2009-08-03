<?php

class ComicPressComicPost {
  var $post;
  var $attachments = null;
  var $comicpress;

  function ComicPressComicPost($post = null, $comicpress = null) {
    if (!is_null($post)) { $this->post = $post; }
    if (!is_null($comicpress)) { $this->comicpress = $comicpress; }
  }

  function get_comic_image_attachments() {
    if (is_null($this->attachments)) {
      $this->attachments = get_children(array(
         'post_parent' => $this->post->ID,
         'post_type' => 'attachment',
         'post_mime_type' => 'image'
      ));
    }
    return $this->attachments;
  }
  
  function display_attached_images($type = "comic", $limit = null, $size_type = null) {
    if (is_null($size_type)) { $size_type = $type; }
    $found = false;
    $ordering = $this->normalize_comic_image_ordering($this->post->ID);
    if (is_array($ordering[$type])) {
      foreach ($ordering[$type] as $attachment_id) {        
        if (get_post_meta($attachment_id, "comic_image_type", true) == $type) {
          $attachment = get_post($attachment_id);
          $title = (!empty($attachment->post_excerpt) ? $attachment->post_excerpt : $attachment->post_title);
          echo $this->get_comic_img_tag(wp_get_attachment_url($attachment->ID, ''), $size_type, array('title' => $title));
          echo "<br />";
          $found = true;
          
          if (!is_null($limit)) {
            if (--$limit == 0) { break; }
          }
        }
      }
    }
    return $found;
  }
  
  function display_comics() { $this->display_attached_images(); }
  
  function display_archive() {
    if (!$this->display_attached_images('archive', 1)) {
      $this->display_attached_images('comic', 1, 'archive');
    }
  }

  function display_rss() {
    if (!$this->display_attached_images('rss')) {
      $this->display_attached_images('comic', null, 'rss');
    }
  }

  function get_comic_img_tag($url, $type, $additional_parameters = array()) {
    $dimensions = array();
    
    if (isset($this->comicpress->comicpress_options["${type}_dimensions"])) {
      list($width, $height) = explode("x", $this->comicpress->comicpress_options["${type}_dimensions"]);
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
  
  function breakdown_comic_ordering_string($string) {
    $parts = explode(";", $string);
    
    $orderings = array();
    foreach ($parts as $part) {
      $type_key_value = explode(":", $part);
      if (count($type_key_value) == 2) {
        list ($key, $value) = $type_key_value;
        if (preg_match('#[^a-z0-9\_\-]#', $key) == 0) {
          $orderings[$key] = array();
          $values = explode(",", $value);
          foreach ($values as $value) {
            $value = trim($value);
            if (is_numeric($value)) {
              $orderings[$key][] = $value;
            } 
          } 
        }
      }
    }
    
    return $orderings;
  }
  
  function build_comic_ordering_string($order) {
    $result_string_parts = array();
    
    foreach ($order as $key => $values) {
      $result_string_parts[] = "${key}:" . implode(",", $values);
    }
    sort($result_string_parts);
    return implode(";", $result_string_parts);
  }
  
  function normalize_comic_image_ordering() {
    if (is_array($this->get_comic_image_attachments())) {
      $ordering_by_type = array();
      $ordering_types = get_post_meta($this->post->ID, 'comic_ordering', true);
      if (!empty($ordering_types)) { $ordering_types = $this->breakdown_comic_ordering_string($ordering_types); }
      
      $comic_image_ordering = array();
      $found_post_ids = array();
      if (!empty($ordering_types)) {
        foreach ($ordering_types as $type => $post_ids) {
          $comic_image_ordering[$type] = array();
          foreach ($post_ids as $ordering_post_id) {
            foreach ($this->get_comic_image_attachments() as $attachment) {
              if (!isset($found_post_ids[$attachment->ID])) {
                if ($attachment->ID == $ordering_post_id) {              
                  $comic_image_ordering[$type][] = $attachment->ID;
                  $found_post_ids[$ordering_post_id] = true;
                } 
              }
            }
          }
        }
      }
      
      $remaining_posts_to_sort = array();
      foreach ($this->get_comic_image_attachments() as $attachment) {
        $comic_image_type = get_post_meta($attachment->ID, 'comic_image_type', true);
        
        if (!empty($comic_image_type)) {
          if (!isset($found_post_ids[$attachment->ID])) {
            if (!isset($remaining_posts_to_sort[$comic_image_type])) {
              $remaining_posts_to_sort[$comic_image_type] = array();
            }
            $remaining_posts_to_sort[$comic_image_type][] = $attachment->ID;
          }
        }
      }
      
      foreach ($remaining_posts_to_sort as $type => $posts) {
        usort($remaining_posts_to_sort[$type], array(&$this, 'sort_remaining_comic_images')); 
      }
      
      $result = array_merge($comic_image_ordering, $remaining_posts_to_sort);

      update_post_meta($this->post->ID, 'comic_ordering', $this->build_comic_ordering_string($result));
      return $result;
    }
  }
  
  function sort_remaining_comic_images($a, $b) {
    return strtotime($a->post_date) - strtotime($b->post_date); 
  }
  
  function change_comic_image_ordering($requested_new_order) {
    $orderings = array();
    if (($order_string = get_post_meta($this->post->ID, 'comic_ordering', true)) !== false) {
      $orderings = $this->breakdown_comic_ordering_string($order_string);
    }
    
    $new_order = array();
    
    foreach ($orderings as $type => $current_order) {
      $new_order[$type] = array();
      $sort_by_position = array();
      foreach ($requested_new_order[$type] as $id => $position) {
        if (!isset($sort_by_position[$position])) {
          $sort_by_position[$position] = array(); 
        }
        $sort_by_position[$position][] = $id;
      }
      ksort($sort_by_position);
      $requested_order = array();
      foreach ($sort_by_position as $position => $ids) {
        sort($ids);
        $requested_order = array_merge($requested_order, $ids);
      }
      
      $requested_order = array_merge($requested_order, array_diff($current_order, $requested_order));
      
      foreach ($requested_order as $requested_comic) {
        if (in_array($requested_comic, $current_order)) {
          $new_order[$type][] = $requested_comic;
        }
      }
    }
    
    update_post_meta($this->post->ID, 'comic_ordering', $this->build_comic_ordering_string($new_order));
  }
}

?>