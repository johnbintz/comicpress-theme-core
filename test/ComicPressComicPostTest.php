<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressComicPost.inc');

class ComicPressComicPostTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->p = new ComicPressComicPost();
  }

  function providerTestGenerateComicTag() {
    return array(
      array(
        "150x150",
      ),
      array(
        false,
      )
    );
  }
  
  /**
   * @dataProvider providerTestGenerateComicTag
   */
  function testGenerateComicImgTag($dimensions) {
    $this->p->comicpress = (object)array(
      'comicpress_options' => array(
        'comic_dimensions' => $dimensions
      )
    );
    
    $source = $this->p->get_comic_img_tag("test.gif", "comic");

    if (count($parts = explode("x", $dimensions)) == 2) {
      list($width, $height) = $parts;
      $dimensions = compact('width', 'height');
    }

    foreach (array('width', 'height') as $field) {
      if (isset($dimensions[$field])) {
        $this->assertTrue(strpos($source, "${field}=\"{$dimensions[$field]}\"") !== false, $field);
      } else {
        $this->assertTrue(strpos($source, "${field}=") === false, 'not set');
      }
    }
  }
  
  function testNormalizeAttachmentSorting() {
    update_post_meta(1, 'comic_order', '2,1')
  }
}

?>