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
    if (is_array($this->get_comic_image_attachments())) {
      foreach ($this->get_comic_image_attachments() as $attachment) {
        if (get_post_meta($attachment->ID, "comic_image_type", true) == $type) {
          echo $this->get_comic_img_tag(wp_get_attachment_url($attachment->ID, ''), $size_type, array('title' => $attachment->post_title));
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
  
  function normalize_attachment_sorting() {
  
  }
}

?>