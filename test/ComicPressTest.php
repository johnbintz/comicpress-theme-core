<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../classes/ComicPress.inc');

class ComicPressTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->cp = new ComicPress();
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
  
  function testGetAllComicCategoriesAsCatString() {
    $this->cp->category_tree = array("0/1", "0/2", "0/3");
    $this->assertEquals("1,2,3", $this->cp->get_all_comic_categories_as_cat_string());
  }
  
  function providerTestInComicCategory() {
    return array(
      array(array(1), false),
      array(array(2), true)
    );
  }
  
  /**
   * @dataProvider providerTestInComicCategory
   */
  function testInComicCategory($post_categories, $is_in_category) {
    $this->cp->category_tree = array('0/2');
    wp_set_post_categories(1, $post_categories);
    
    $this->assertEquals($is_in_category, $this->cp->in_comic_category(1));
  }
}

?>