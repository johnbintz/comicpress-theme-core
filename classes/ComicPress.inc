<?php

class ComicPress {
  var $comicpress_options = array(
    'comic_category_id' => 1,
    'comic_dimensions' => '760x',
    'rss_dimensions' => '350x',
    'archive_dimensions' => '125x'
  );

  function load() {
    $result = get_option('comicpress-options');
    if (is_array($result)) {
      $this->comicpress_options = $result;
    }
  }
  
  function save() {
    if (is_array($this->comicpress_options)) {
      update_option('comicpress-options', $this->comicpress_options);
    }
  }

  function init() {
    $this->load();
  }
  
  function get_comic_img_tag($url, $type, $additional_parameters = array()) {
    $dimensions = array();
    if (isset($this->comicpress_options["${type}_dimensions"])) {
      list($width, $height) = explode("x", $this->comicpress_options["${type}_dimensions"]);
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
}

?>