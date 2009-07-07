<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../functions.php');

class FunctionsTest extends PHPUnit_Framework_TestCase {
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
    global $comicpress_options_admin;
    
    $comicpress_options_admin->comicpress_options['comic_dimensions'] = $dimensions;
    
    ob_start();
    the_comic_img_tag("test.gif", "comic");
    $source = ob_get_clean();

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