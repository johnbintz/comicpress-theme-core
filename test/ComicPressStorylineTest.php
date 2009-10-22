<?php

require_once('MockPress/mockpress.php');
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressStoryline.inc');

class ComicPressStorylineTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    
    $this->css = new ComicPressStoryline();
  }

  function providerTestCreateStorylineStructure() {
    return array(
      array(
        false,
        false,
        false
      ),
      array(
        array('0'),
        false,
        false
      ),
      array(
        array('1'),
        false,
        false
      ),
      array(
        array(array(0,1)),
        false,
        false
      ),
      array(
        array('0/1'),
        array('1' => array()),
        1
      ),
      array(
        array('0/1', '0/1/2'),
        array('1' => array('next' => 2), '2' => array('parent' => 1, 'previous' => 1)),
        1
      ),
      array(
        array('0/1', '0/1/2', '0/1/3'),
        array(
          '1' => array('next' => 2),
          '2' => array('parent' => 1, 'previous' => 1, 'next' => 3),
          '3' => array('parent' => 1, 'previous' => 2),
        ),
        1
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5'),
        array(
          '1' => array('next' => 2),
          '2' => array('parent' => 1, 'next' => 3, 'previous' => 1),
          '3' => array('parent' => 2, 'next' => 4, 'previous' => 2),
          '4' => array('parent' => 2, 'next' => 5, 'previous' => 3),
          '5' => array('parent' => 1, 'previous' => 4),
        ),
        1
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5', '0/1/5/6', '0/1/5/7', '0/1/5/8', '0/1/9'),
        array(
          '1' => array('next' => 2),
          '2' => array('parent' => 1, 'next' => 3, 'previous' => 1),
          '3' => array('parent' => 2, 'next' => 4, 'previous' => 2),
          '4' => array('parent' => 2, 'next' => 5, 'previous' => 3),
          '5' => array('parent' => 1, 'next' => 6, 'previous' => 4),
          '6' => array('parent' => 5, 'next' => 7, 'previous' => 5),
          '7' => array('parent' => 5, 'next' => 8, 'previous' => 6),
          '8' => array('parent' => 5, 'next' => 9, 'previous' => 7),
          '9' => array('parent' => 1, 'previous' => 8),
        ),
        1
      ),
    );
  }

  /**
   * @dataProvider providerTestCreateStorylineStructure
   */
  function testCreateStorylineStructure($input, $expected_structure, $expected_root_category) {
    $this->assertEquals(is_array($expected_structure), $this->css->create_structure($input));
    $this->assertEquals($expected_structure, $this->css->_structure);
    $this->assertEquals($expected_root_category, $this->css->root_category);
  }
  
  function providerTestGetFields() {
    return array(
      array('parent', 1, false),
      array('parent', 2, 1),
      array('next', 2, 3),
      array('next', 3, 4),
      array('valid', 1, array('next', 'current')),
      array('valid', 6, false),
    );
  }
  
  /**
   * @dataProvider providerTestGetFields
   */
  function testGetFields($field, $category, $expected_value) {
    $this->css->_structure = array(
      '1' => array('next' => 2),
      '2' => array('parent' => 1, 'previous' => 1, 'next' => 3),
      '3' => array('parent' => 2, 'next' => 4, 'previous' => 2),
      '4' => array('parent' => 2, 'previous' => 3)
    );
    
    $this->assertEquals($expected_value, $this->css->{$field}($category));
  }

  function providerTestGetValidNav() {
    return array(
      array(array(1),   array('next', 'current')),
      array(array(1,2), false),
      array(array(1,4), array('next', 'current')),
      array(array(2),   array('previous', 'next', 'current')),
      array(array(3),   array('previous', 'current')),
    );
  }
  
  /**
   * @dataProvider providerTestGetValidNav
   */
  function testGetValidNav($post_categories, $expected_navigation) {
    wp_set_post_categories(1, $post_categories);
  
    $this->css->_structure = array(
      '1' => array('next' => 2),
      '2' => array('previous' => 1, 'next' => 3),
      '3' => array('previous' => 2)
    );

    $this->assertEquals($expected_navigation, $this->css->get_valid_nav(1));
  }

  function providerTestGetValidPostCategory() {
    return array(
      array(array(1,2), false),
      array(array(1,3), false),
      array(array(1), 1),
    );
  }

  /**
   * @dataProvider providerTestGetValidPostCategory
   */
  function testGetValidPostCategory($post_categories, $expected_result) {
    $css = $this->getMock('ComicPressStoryline', array('valid'));
    $css->expects($this->any())->method('valid')->will($this->returnValue(true));
    
    wp_set_post_categories(1, $post_categories);

    $this->assertEquals($expected_result, $css->get_valid_post_category(1));
  }
}

?>