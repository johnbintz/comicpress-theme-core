<?php

require_once('MockPress/mockpress.php');
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressDBInterface.inc');

class ComicPressDBInterfaceTest extends PHPUnit_Framework_TestCase {
  function testSingleton() {
    $a = ComicPressDBInterface::get_instance();
    $this->assertTrue(!isset($a->test));
    $a->test = "test";
    $this->assertEquals("test", $a->test);

    $b = ComicPressDBInterface::get_instance();
    $this->assertEquals("test", $b->test);
  }

  function testSetComicCategories() {
    $dbi = $this->getMock('ComicPressDBInterface', array('_get_categories'));

    $dbi->expects($this->once())->method('_get_categories')->will($this->returnValue(array(
      (object)array('term_id' => 1),
      (object)array('term_id' => 2),
      (object)array('term_id' => 3),
      (object)array('term_id' => 4)
    )));

    $dbi->set_comic_categories(array(2,3));

    $this->assertEquals(array(1,2,3,4), $dbi->_all_categories);
    $this->assertEquals(array(1,4), $dbi->_non_comic_categories);
  }

  function providerTestGetCategoriesToExclude() {
    return array(
      array(null, array(1 ,4)),
      array(2, array(1, 3, 4)),
    );
  }

  /**
   * @dataProvider providerTestGetCategoriesToExclude
   */
  function testGetCategoriesToExclude($category, $expected_results) {
    $dbi = ComicPressDBInterface::get_instance();
    $dbi->_all_categories = array(1,2,3,4);
    $dbi->_non_comic_categories = array(1,4);

    $this->assertEquals($expected_results, $dbi->_get_categories_to_exclude($category));
  }
}

?>