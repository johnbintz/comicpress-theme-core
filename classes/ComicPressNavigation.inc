<?php

require_once('ComicPressStoryline.inc');
require_once('ComicPressDBInterface.inc');

class ComicPressNavigation {
  function init($storyline) {
    $this->_storyline = $storyline;
    $this->_dbi       = ComicPressDBInterface::get_instance();
  }

  function get_post_nav($post) {
    $nav = array();

    // post
    foreach (array('previous', 'next') as $field) {
      $nav[$field] = $this->_dbi->{"get_${field}_comic"}(null, $post);
    }
  }
}

?>