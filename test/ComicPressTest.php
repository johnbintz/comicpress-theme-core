<?php

require_once('PHPUnit/Framework.php');
require_once('MockPress/mockpress.php');
require_once(dirname(__FILE__) . '/../classes/ComicPress.inc');

class ComicPressTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    global $post;
    
    _reset_wp();
    unset($post);
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

  function providerTestGetNavComics() {
    return array(
      array(
        array(
          'first'    => 1,
          'previous' => 2,
          'next'     => 4,
          'last'     => 5
        ),
        3,
        array(
          'first'    => true,
          'previous' => true,
          'next'     => true,
          'last'     => true
        )
      ),
      array(
        array(
          'first'    => 1,
          'previous' => false,
          'next'     => false,
          'last'     => 1
        ),
        1,
        array(
          'first'    => false,
          'previous' => false,
          'next'     => false,
          'last'     => false
        )
      ),
      array(
        array(
          'first'    => 1,
          'previous' => false,
          'next'     => 3,
          'last'     => 3
        ),
        1,
        array(
          'first'    => false,
          'previous' => false,
          'next'     => false,
          'last'     => true
        )
      ),
      array(
        array(
          'first'    => 1,
          'previous' => 1,
          'next'     => false,
          'last'     => 3
        ),
        3,
        array(
          'first'    => true,
          'previous' => false,
          'next'     => false,
          'last'     => false
        )
      )
    );
  }

  /**
   * @dataProvider providerTestGetNavComics
   */
  function testGetNavComics($nav_comics, $given_post, $expected_shows) {
    global $post;
    
    $cp = $this->getMock('ComicPress', array('get_first_comic', 'get_last_comic', 'get_previous_comic', 'get_next_comic'));
    foreach ($nav_comics as $key => $result) {
      $return = (is_numeric($result)) ? (object)array('ID' => $result) : false;
      $cp->expects($this->once())->method("get_${key}_comic")->will($this->returnValue($return));
    }
    
    $post = (is_numeric($given_post)) ? (object)array('ID' => $given_post) : false;
    
    $comic_posts = $cp->get_nav_comics();
    
    foreach ($expected_shows as $show => $expected) {
      $this->assertEquals($expected, $comic_posts["show_${show}"], $show);
    }
  }
  
  function providerTestGetNavStorylineEnabled() {
    return array(
      array(
        array('10'),
        array(
          '10' => array(
            '10' => array('previous' => '9', 'next' => '11')
          )
        ),
        array(
          '9'  => true,
          '11' => true
        ),
        array(
          'prior' => true,
          'upcoming' => true
        )
      ),
      array(
        array('10', '20'),
        array(
          '10' => array(
            '10' => array('previous' => '7', 'next' => '3')
          ),
          '20' => array(
            '20' => array('previous' => '9', 'next' => '11')
          ),
        ),
        array(
          '9'  => true,
          '11' => true
        ),
        array(
          'prior' => true,
          'upcoming' => true
        )
      ),
      array(
        array('10'),
        array(
          '10' => array(
            '10' => array('previous' => '7', 'next' => '3')
          ),
        ),
        array(
          '15'  => true,
          '20' => true
        ),
        array(
          'prior' => false,
          'upcoming' => false
        )
      ),
      array(
        array('10'),
        array(
          '10' => array(
            '7' => array('previous' => '15', 'next' => '20')
          ),
        ),
        array(
          '15'  => true,
          '20' => true
        ),
        array(
          'prior' => true,
          'upcoming' => true
        )
      ),
    );
  }
  
  /**
   * @dataProvider providerTestGetNavStorylineEnabled
   */
  function testGetNavComicsStorylineEnabled($post_categories, $previous_next_categories, $terminal_comics, $expected_results) {
    $cp = $this->getMock('ComicPress', array('get_sorted_post_categories', 'get_previous_next_categories', 'get_last_comic', 'get_first_comic'));
    
    $cp->expects($this->once())->method('get_sorted_post_categories')->will($this->returnValue($post_categories));
    $cp->expects($this->any())->method('get_previous_next_categories')->will($this->returnCallback(array(&$this, 'callbackGetPreviousNextCategories')));
    $cp->expects($this->any())->method('get_first_comic')->will($this->returnCallback(array(&$this, 'callbackGetTerminalComic')));
    $cp->expects($this->any())->method('get_last_comic')->will($this->returnCallback(array(&$this, 'callbackGetTerminalComic')));
    
    $this->_previous_next_categories = $previous_next_categories;
    $this->_terminal_comics = $terminal_comics;
    
    $result = $cp->get_storyline_nav_comics();
    foreach ($expected_results as $field => $value) {
      $this->assertEquals($value, $result["show_${field}"]);
    }
  }
  
  function callbackGetPreviousNextCategories($id) {
    return $this->_previous_next_categories[$id];
  }
  
  function callbackGetTerminalComic($id) {
    return isset($this->_terminal_comics[$id]) ? (object)array() : false;
  }

  function providerTestGetPreviousNextCategories() {
    return array(
      array(
        array(
          '0/1'
        ),
        1,
        array()
      ),
      array(
        array(
          '0/1',
          '0/1/2'
        ),
        2,
        array()
      ),
      array(
        array(
          '0/1',
          '0/1/2',
          '0/1/3',
        ),
        2,
        array(
          '1' => array('next' => 3)
        )
      ),
      array(
        array(
          '0/1',
          '0/1/6',
          '0/1/2',
          '0/1/2/4',
          '0/1/2/5',
          '0/1/3',
        ),
        5,
        array(
          '2' => array('previous' => 4),
          '1' => array('previous' => 6, 'next' => 3),
        )
      ),
    );
  }
  
  /**
   * @dataProvider providerTestGetPreviousNextCategories
   */
  function testGetPreviousNextCategories($category_tree, $current_category, $expected_prev_nexts) {
    $this->cp->category_tree = $category_tree;
    
    $this->assertEquals($expected_prev_nexts, $this->cp->get_previous_next_categories($current_category));
  }
  
  function providerTestGetLayoutChoices() {
    return array(
      array(
        array(),
        array()
      ),
      array(
        array(
          'layout.php' => <<<FILE
            Test
FILE
        ),
        array()
      ),
      array(
        array(
          'layout.php' => <<<FILE
/*
Layout Name: Test
Sidebars: left,right
*/ 
FILE
        ),
        array('layout.php' => array(
          'Layout Name' => 'Test',
          'Sidebars' => 'left,right',
        ))
      ),
    );
  }
  
  /**
   * @dataProvider providerTestGetLayoutChoices
   */
  function testGetLayoutChoices($files, $expected_results) {
    $cp = $this->getMock('ComicPress', array('_glob', '_file_get_contents'));
    
    _set_template_directory('/test');
    
    $file_names = array();
    foreach (array_keys($files) as $file) { $file_names[] = '/test/layouts/' . $file; }
    
    $cp->expects($this->once())->method('_glob')->with('/test/layouts/*')->will($this->returnValue($file_names));
    foreach ($files as $file => $contents) {
      $cp->expects($this->once())->method('_file_get_contents')->with('/test/layouts/' . $file)->will($this->returnValue($contents)); 
    }
    
    $this->assertEquals($expected_results, $cp->get_layout_choices());
  }
  
  function providerTestGetSortedPostCategories() {
    return array(
      array(
        array(1),
        array('0/1'),
        array(1)
      ),
      array(
        array(2, 1),
        array('0/1', '0/1/2'),
        array(1, 2)
      ),
      array(
        array(2, 1),
        array('0/1', '0/1/3', '0/1/2'),
        array(1, 2)
      ),
    );
  }
  
  /**
   * @dataProvider providerTestGetSortedPostCategories
   */
  function testGetSortedPostCategories($post_categories, $category_tree, $expected_sort_order) {
    $this->cp->category_tree = $category_tree;
    
    wp_set_post_categories(1, $post_categories);
    
    $this->assertEquals($expected_sort_order, $this->cp->get_sorted_post_categories((object)array('ID' => 1)));
  }
  
  function testSetupMulticomicPartialPaths() {
    $cp = $this->getMock('ComicPress', array('_is_dir'));
    
    wp_set_post_categories(1, array('2', '3'));
    
    add_category('2', (object)array('slug' => 'test-one'));
    add_category('3', (object)array('slug' => 'test-two'));
    
    $cp->expects($this->at(0))->method('_is_dir')->with('/subthemes/test-one')->will($this->returnValue(true));
    $cp->expects($this->at(1))->method('_is_dir')->with('/subthemes/test-two')->will($this->returnValue(false));
    
    $cp->setup_multicomic_partial_paths(1);
    
    $this->assertEquals(array('/subthemes/test-one'), $cp->partial_paths);
  }
  
  function providerTestGetOverridePartials() {
    return array(
      array(
        array('partials'),
        array('index'),
        array('partials/index'),
        array('partials/index', true)
      ),
      array(
        array('partials'),
        array('index'),
        array('partials/single'),
        false
      )
    );
  }
  
  /**
   * @dataProvider providerTestGetOverridePartials
   */
  function testGetOverridePartials($partial_paths, $requested_partials, $override_partials, $expected_result) {
    $this->cp->partial_paths = $partial_paths;
    foreach ($override_partials as $partial) {
      $this->cp->comicpress_options['override_partials'][$partial] = true;
    }
    $this->assertEquals($expected_result, $this->cp->get_options_partial($requested_partials));
  }
}

?>