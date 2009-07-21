<?php

require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__) . '/../../../../mockpress/mockpress.php');
require_once(dirname(__FILE__) . '/../../../classes/ComicPressAddon.inc');
require_once(dirname(__FILE__) . '/../Core.inc');

class CoreTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    _reset_wp();
    $_POST = array();
    $this->core = new ComicPressAddonCore();
  }
  
  function testShowOptionsPage() {
    $nonce = wp_create_nonce('comicpress');
  
    ob_start();
    $this->core->render_admin();
    $source = ob_get_clean();

    $this->assertTrue(($xml = _to_xml($source)) !== false);
    foreach (array(
      '//input[@name="cp[_nonce]" and @value="' . $nonce . '"]' => true,
      '//select[@name="cp[comic_category_id]"]' => true
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
    foreach ($this->core->get_root_categories() as $category) {
      $result_ids[] = $category->term_id;
    }
    
    $this->assertEquals($expected_result, $result_ids);
  }

  function testCreateCategoryOptions() {
    add_category(1, (object)array('name' => 'test-one'));
    add_category(2, (object)array('name' => 'test-two'));
    
    foreach(array(
      array(1,2),
      array(get_category(1), get_category(2))
    ) as $category_test) {
      $source = $this->core->create_category_options($category_test, 1);
      
      $this->assertTrue(($xml = _to_xml($source, true)) !== false);
      
      foreach (array(
        '//option[@value="1" and @selected="selected"]' => "test-one",
        '//option[@value="2"]' => "test-two",        
      ) as $xpath => $value) {
        $this->assertTrue(_xpath_test($xml, $xpath, $value), $xpath);
      }
    }
  }

  function testCreateDimensionSelector() {
    $source = $this->core->create_dimension_selector("test", "760x340");
    
    $this->assertTrue(($xml = _to_xml($source, true)) !== false);
    
    foreach (array(
      '//input[@name="test[width]" and @value="760"]' => true,
      '//input[@name="test[height]" and @value="340"]' => true,
    ) as $xpath => $value) {
      $this->assertTrue(_xpath_test($xml, $xpath, $value), $xpath);      
    }    
  }
 
  function providerTestHandleUpdate() {
    return array(
      array(
        array('comic_category_id' => 1),
        array('comic_category_id' => 2),
        array('comic_category_id' => 1)
      ),
      array(
        array('comic_category_id' => 1),
        array('cp' => array(
          'comic_category_id' => 2),
        ),
        array('comic_category_id' => 2)      
      ),
      array(
        array('comic_category_id' => 1),
        array('cp' => array(
          'comic_category_id' => "cat"),
        ),
        array('comic_category_id' => 1)      
      ),
      array(
        array('comic_category_id' => 1),
        array('cp' => array(
          'comic_category_id' => 3
        )),
        array('comic_category_id' => 1)      
      ),
      array(
        array('comic_dimensions' => '150x150'),
        array('cp' => array(
          'comic_dimensions' => 'test'
        )),
        array('comic_dimensions' => '150x150')
      ),
      array(
        array('comic_dimensions' => '150x150'),
        array('cp' => array(
          'comic_dimensions' => array(
            'width' => '150',
            'height' => ''
          )
        )),
        array('comic_dimensions' => '150x')
      ),
      array(
        array('comic_dimensions' => '150x150'),
        array('cp' => array(
          'comic_dimensions' => array(
            'width' => '150.1',
            'height' => ''
          )
        )),
        array('comic_dimensions' => '150x150')
      ),
    );
  }

  /**
   * @dataProvider providerTestHandleUpdate
   */
  function testHandleUpdate($original, $change, $new) {
    $this->core->comicpress = $this->getMock('ComicPress', array('save', 'init'));
    $this->core->comicpress->comicpress_options = array(
      'comic_category_id' => 1,
      'comic_dimensions' => '760x',
      'rss_dimensions' => '350x',
      'archive_dimensions' => '125x'
    );
    $this->core->comicpress->comicpress_options = array_merge($this->core->comicpress->comicpress_options, $original);
    
    add_category(2, (object)array('name' => 'test'));
    
    $_POST = $change;

    $this->core->handle_update_comicpress_options();
    
    foreach ($new as $key => $value) {
      $this->assertEquals($value, $this->core->comicpress->comicpress_options[$key]);
    }
  }
  
  function testGetStorylineMoveStatuses() {
    $this->core->comicpress = (object)array(
      'category_tree' => array(
        '0/1',
        '0/1/2',
        '0/1/2/3',
        '0/1/2/4',
        '0/1/2/5',
        '0/1/6',
        '0/1/6/7',
        '0/1/6/8',
        '0/1/9',
        '0/1/9/10',
        '0/1/11'
      )
    );
    
    $this->assertEquals(array(
      '0/1' => array(false, false),
      '0/1/2' => array(false, true),
      '0/1/2/3' => array(false, true),
      '0/1/2/4' => array(true, true),
      '0/1/2/5' => array(true, false),
      '0/1/6' => array(true, true),
      '0/1/6/7' => array(false, true),
      '0/1/6/8' => array(true, false),
      '0/1/9' => array(true, true),
      '0/1/9/10' => array(false, false),
      '0/1/11' => array(true, false)
    ), $this->core->get_storyline_move_statuses());
  }
  
  function providerTestMoveStorylineCategoryOrder() {
    return array(
      array(
        array('0/1', '0/1/2', '0/1/3'),
        3, -1,
        array('0/1', '0/1/3', '0/1/2')
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/4', '0/1/3'),
        3, -1,
        array('0/1', '0/1/3', '0/1/2', '0/1/2/4')
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/4', '0/1/3', '0/1/3/5'),
        3, -1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/2', '0/1/2/4')
      ),
      array(
        array('0/1', '0/1/2', '0/1/3', '0/1/3/5'),
        3, -1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/2')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7'),
        7, -1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/7', '0/1/3/6')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4'),
        7, -1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/7', '0/1/3/6', '0/1/4')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4'),
        3, 1,
        array('0/1', '0/1/4', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7')
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/4', '0/1/3'),
        2, 1,
        array('0/1', '0/1/3', '0/1/2', '0/1/2/4')
      ),
      array(
        array('0/1', '0/1/2', '0/1/2/4', '0/1/3', '0/1/3/5'),
        2, 1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/2', '0/1/2/4')
      ),
      array(
        array('0/1', '0/1/2', '0/1/3', '0/1/3/5'),
        2, 1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/2')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7'),
        6, 1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/7', '0/1/3/6')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4'),
        6, 1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/7', '0/1/3/6', '0/1/4')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4'),
        5, 1,
        array('0/1', '0/1/3', '0/1/3/6', '0/1/3/5', '0/1/3/7', '0/1/4')
      ),
      array(
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4', '0/1/4/9'),
        9, 1,
        array('0/1', '0/1/3', '0/1/3/5', '0/1/3/6', '0/1/3/7', '0/1/4', '0/1/4/9')
      ),
    );
  }
  
  /**
   * @dataProvider providerTestMoveStorylineCategoryOrder
   */
  function testMoveStorylineCategoryOrder($original_order, $category, $direction, $new_order) {
    $this->core->comicpress = (object)array(
      'category_tree' => $original_order,
      'comicpress_options' => array(
        'category_order' => $original_order
      )
    );
    
    $this->core->move_storyline_category_order($category, $direction);
    
    $this->assertEquals($new_order, $this->core->comicpress->comicpress_options['category_order']);
  }
  
  function providerTestUpdateAttachments() {
    return array(
      array(
        array(
          'post_meta' => array(),
        ),
        array(
          'comic_image_type' => "test"
        ),
        array(
          'post_meta' => array(
            'comic_image_type' => "test"
          ),
        ),
      ),
      array(
        array(
          'post' => array(
            'post_parent' => 0
          ),
        ),
        array(
          'post_parent' => "2"
        ),
        array(
          'post' => array(
            'post_parent' => 0
          ),
        ),
      ),  
      array(
        array(
          'post' => array(
            'post_parent' => 0
          ),
        ),
        array(
          'post_parent' => "2",
          'auto_attach' => 1
        ),
        array(
          'post' => array(
            'post_parent' => 2
          ),
        ),
      )
    ); 
  }
  
  /**
   * @dataProvider providerTestUpdateAttachments
   */
  function testUpdateAttachments($original_settings, $changes, $expected_settings) {
    foreach ($original_settings as $settings_type => $settings) {
      switch ($settings_type) {
        case "post_meta":
          foreach ($settings as $key => $value) {
            update_post_meta(1, $key, $value);
          }
          break;
        case "post":
          wp_insert_post((object)array_merge(array(
            'ID' => 1
          ), $settings));
          break;
      }
    }
    
    $_POST = array(
      'attachments' => array('1' => $changes)
    );
    
    $this->core->handle_update_attachments();
    
    foreach ($expected_settings as $settings_type => $settings) {
      switch ($settings_type) {
        case "post_meta":
          foreach ($settings as $key => $value) {
            $this->assertEquals($value, get_post_meta(1, $key, true));
          }
          break; 
        case "post":
          $post = get_post(1);
          foreach ($settings as $key => $value) {
            $this->assertEquals($value, $post->{$key});
          }
      }
    }    
  }
}

?>
