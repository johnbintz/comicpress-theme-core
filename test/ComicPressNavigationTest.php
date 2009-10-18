<?php

require_once('MockPress/mockpress.php');
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressNavigation.inc');

class ComicPressNavigationTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->nav = new ComicPressNavigation();
  }
  
  function testSomething() {
    $this->markTestIncomplete();
  }
}

?>