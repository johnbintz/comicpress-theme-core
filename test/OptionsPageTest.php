<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../options.php');

class OptionsPageTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $this->admin = new ComicPressOptionsAdmin();
  }
  
  function testShowOptionsPage() {
    $nonce = wp_create_nonce('comicpress');
  
    ob_start();
    $this->admin->render_admin();
    $source = ob_get_clean();

    $this->assertTrue(($xml = _to_xml($source)) !== false);
    foreach (array(
      '//input[@name="cp[_nonce]" and @value="' . $nonce . '"]' => true
    ) as $xpath => $value) {
      $this->assertTrue(_xpath_test($xml, $xpath, $value), $xpath);
    }
  }
  
  function providerTestGetRootComicCategories() {
    return array(
      array(array(), array()),
      array(
        array(
          array('id' => 1, 'parent' => 0),
          array('id' => 2, 'parent' => 1)
        ),
        array(1)
      )
    );
  }
  
  /**
   * @dataProvider providerTestGetRootComicCategories
   */
  function testGetRootCategories($categories, $expected_result) {
    foreach ($categories as $category) {
      add_category($category['id'], (object)$category);
    }
    
    $result_ids = array();
    foreach ($this->admin->get_root_categories() as $category) {
      $result_ids[] = $category->term_id;
    }
    
    $this->assertEquals($expected_result, $result_ids);
  }
}

?>
