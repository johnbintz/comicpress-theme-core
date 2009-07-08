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
}

?>