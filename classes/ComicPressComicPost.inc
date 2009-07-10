<?php

class ComicPressComicPost {
  var $post;
  var $attachments;
  var $comicpress;

  function ComicPressComicPost($post = null, $comicpress = null) {
    if (!is_null($post)) { $this->post = $post; }
    if (!is_null($comicpress)) { $this->comicpress = $comicpress; }
  }

  function get_comic_image_attachments() {
    if (!isset($this->attachments)) {
      $this->attachments = get_children(array(
         'post_parent' => $this->post->ID,
         'post_type' => 'attachment',
         'post_mime_type' => 'image'
      ));
    }
    return $this->attachments;
  }
  
  function display_comics() {
    foreach ($this->get_comic_image_attachments() as $attachment) {
      echo $this->get_comic_img_tag(wp_get_attachment_url($attachment->ID), "comic", array('title' => $attachment->post_title));
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