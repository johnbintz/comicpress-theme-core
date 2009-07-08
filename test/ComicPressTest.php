<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../classes/ComicPress.inc');

class ComicPressTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->cp = new ComicPress();
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
    $this->cp->comicpress_options['comic_dimensions'] = $dimensions;
    
    $source = $this->cp->get_comic_img_tag("test.gif", "comic");

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
}

?>