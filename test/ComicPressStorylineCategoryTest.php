<?php

require_once('MockPress/mockpress.php');
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../classes/ComicPressStorylineCategory.inc');

class ComicPressStorylineCategoryTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
  }
  
  function testGetAdjacentCategories() {
    $sc = new ComicPressStorylineCategory();
  }
}

?>