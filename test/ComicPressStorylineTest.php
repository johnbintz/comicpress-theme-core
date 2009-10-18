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
        false
      ),
      array(
        array('0'),
        false
      ),
      array(
        array('1'),
        false
      ),
      array(
        array(array(0,1)),
        false
      ),
      array(
        array('0/1'),
        array('1' => array())
      ),
      array(
        array('0/1', '0/1/2'),
        array('1' => array('upcoming' => 2), '2' => array('parent' => 1, 'prior' => 1))
      ),
      array(
        array('0/1', '0/1/2', '0/1/3'),
        array(
          '1' => array('upcoming' => 2), 
          '2' => array('parent' => 1, 'next' => 3, 'prior' => 1, 'upcoming' => 3),
          '3' => array('parent' => 1, 'previous' => 2, 'prior' => 2),
        )
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5'),
        array(
          '1' => array('upcoming' => 2), 
          '2' => array('parent' => 1, 'next' => 5, 'upcoming' => 3, 'prior' => 1),
          '3' => array('parent' => 2, 'next' => 4, 'upcoming' => 4, 'prior' => 2),
          '4' => array('parent' => 2, 'previous' => 3, 'upcoming' => 5, 'prior' => 3),
          '5' => array('parent' => 1, 'previous' => 2, 'prior' => 4),
        )
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5', '0/1/5/6', '0/1/5/7', '0/1/5/8', '0/1/9'),
        array(
          '1' => array('upcoming' => 2), 
          '2' => array('parent' => 1, 'next' => 5, 'upcoming' => 3, 'prior' => 1),
          '3' => array('parent' => 2, 'next' => 4, 'upcoming' => 4, 'prior' => 2),
          '4' => array('parent' => 2, 'previous' => 3, 'upcoming' => 5, 'prior' => 3),
          '5' => array('parent' => 1, 'previous' => 2, 'next' => 9, 'upcoming' => 6, 'prior' => 4),
          '6' => array('parent' => 5, 'next' => 7, 'upcoming' => 7, 'prior' => 5),
          '7' => array('parent' => 5, 'previous' => 6, 'next' => 8, 'upcoming' => 8, 'prior' => 6),
          '8' => array('parent' => 5, 'previous' => 7, 'upcoming' => 9, 'prior' => 7),
          '9' => array('parent' => 1, 'previous' => 5, 'prior' => 8),
        )
      ),
    );
  }

  /**
   * @dataProvider providerTestCreateStorylineStructure
   */
  function testCreateStorylineStructure($input, $expected_structure) {
    $this->assertEquals(is_array($expected_structure), $this->css->create_structure($input));
    $this->assertEquals($expected_structure, $this->css->_structure);
  }
  
  function providerTestGetFields() {
    return array(
      array('parent', 1, false),
      array('parent', 2, 1),
      array('next', 3, 4),
      array('next', 4, false),
      array('previous', 4, 3),
      array('previous', 3, false),
      array('previous', 2, false),
      array('upcoming', 2, 3),
      array('upcoming', 3, 4),
      array('valid', 1, array('upcoming')),
      array('valid', 6, false),
    );
  }
  
  /**
   * @dataProvider providerTestGetFields
   */
  function testGetFields($field, $category, $expected_value) {
    $this->css->_structure = array(
      '1' => array('upcoming' => 2),
      '2' => array('parent' => 1, 'prior' => 1, 'upcoming' => 3),
      '3' => array('parent' => 2, 'next' => 4, 'upcoming' => 4, 'prior' => 2),
      '4' => array('parent' => 2, 'previous' => 3, 'prior' => 3)
    );
    
    $this->assertEquals($expected_value, $this->css->{$field}($category));
  }

  function providerTestGetValidStorylineNav() {
    return array(
      array(array(1),   array('upcoming')),
      array(array(1,2), false),
      array(array(1,4), array('upcoming')),
      array(array(2),   array('prior', 'upcoming', 'next')),
      array(array(3),   array('prior', 'previous')),
    );
  }
  
  /**
   * @dataProvider providerTestGetValidStorylineNav
   */
  function testGetValidStorylineNav($post_categories, $expected_navigation) {
    wp_set_post_categories(1, $post_categories);
  
    $this->css->_structure = array(
      '1' => array('upcoming' => 2),
      '2' => array('prior' => 1, 'upcoming' => 3, 'next' => 3),
      '3' => array('prior' => 2, 'previous' => 2)
    );

    $this->assertEquals($expected_navigation, $this->css->get_valid_storyline_nav(1));
  }
}

?>