<?php

require_once('MockPress/mockpress.php');
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressNavigation.inc');

/**
 * Integration Testing. Just make sure things are called correctly.
 */
class ComicPressNavigationTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->nav = new ComicPressNavigation();
  }
  
  function testGetPostNav() {
    $dbi = $this->getMock('ComicPressDBInterface', array('get_previous_comic', 'get_next_comic'));

    wp_insert_post(array('ID' => 1));
    $post = get_post(1);

    wp_set_post_categories(1, array(1));

    $dbi->expects($this->once())->method('get_previous_comic')->with(null, $post);
    $dbi->expects($this->once())->method('get_next_comic')->with(null, $post);

    $this->nav->_dbi = $dbi;

    $this->nav->get_post_nav($post);
  }
}

?>