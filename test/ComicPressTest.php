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
  
  function testFlattenCategories() {
    $cp = $this->getMock('ComicPress', array('get_all_category_objects_by_id'));
    
    $cp->categories_by_id = array(
      '1' => (object)array(
        'term_id' => 1,
        'parent' => 0
      ),
      '2' => (object)array(
        'term_id' => 2,
        'parent' => 1
      ),
      '3' => (object)array(
        'term_id' => 3,
        'parent' => 0
      )
    );
    
    $this->assertEquals(array('0/1', '0/1/2', '0/3'), $cp->flatten_categories());
  }
  
  function testSeparateCategories() {
    $cp = $this->getMock('ComicPress', array('flatten_categories'));
    
    $cp->category_tree = array('0/1', '0/1/2', '0/3');
    
    $cp->comicpress_options['comic_category_id'] = 1;

    $cp->separate_categories();

    $this->assertEquals(array('0/1', '0/1/2'), $cp->category_tree);
    $this->assertEquals(array('0/3'), $cp->non_comic_categories);
  }
  
  function providerSortComicCategories() {
    return array(
      array(false, array('0/1', '0/2'), array('0/1', '0/2')),
      array(array('0/2', '0/1'), array('0/1', '0/2', '0/3'), array('0/2', '0/1', '0/3'))
    );
  }
  
  /**
   * @dataProvider providerSortComicCategories
   */
  function testSortComicCategories($category_order, $category_tree, $expected_tree) {
    $this->cp->comicpress_options['category_order'] = $category_order;
    $this->cp->category_tree = $category_tree;
    
    $this->assertEquals($expected_tree, $this->cp->sort_comic_categories());
  }
}

?>