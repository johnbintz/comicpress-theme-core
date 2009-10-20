<?php

class ComicPressFilters {
  function comicpress_display_attached_images($images, $post_id, $content) {
    return array($images, $post_id, implode("\n", $images));
  }
}

$__comicpress_filters = new ComicPressFilters();

foreach (array(
  'comicpress_display_attached_images' => 3
) as $filter => $param_count) {
  add_filter($filter, array(&$__comicpress_filters, $filter), 10, $param_count);
}

?>