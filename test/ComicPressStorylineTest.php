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
        array('1' => array(), '2' => array('parent' => 1))
      ),
      array(
        array('0/1', '0/1/2', '0/1/3'),
        array(
          '1' => array(), 
          '2' => array('parent' => 1, 'next' => 3),
          '3' => array('parent' => 1, 'previous' => 2),
        )
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5'),
        array(
          '1' => array(), 
          '2' => array('parent' => 1, 'next' => 5),
          '3' => array('parent' => 2, 'next' => 4),
          '4' => array('parent' => 2, 'previous' => 3),
          '5' => array('parent' => 1, 'previous' => 2),
        )
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/3', '0/1/2/4', '0/1/5', '0/1/5/6', '0/1/5/7', '0/1/5/8', '0/1/9'),
        array(
          '1' => array(), 
          '2' => array('parent' => 1, 'next' => 5),
          '3' => array('parent' => 2, 'next' => 4),
          '4' => array('parent' => 2, 'previous' => 3),
          '5' => array('parent' => 1, 'previous' => 2, 'next' => 9),
          '6' => array('parent' => 5, 'next' => 7),
          '7' => array('parent' => 5, 'previous' => 6, 'next' => 8),
          '8' => array('parent' => 5, 'previous' => 7),
          '9' => array('parent' => 1, 'previous' => 5),
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
}

?>