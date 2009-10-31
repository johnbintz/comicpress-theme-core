<?php

require_once('ComicPressDBInterface.inc');

class ComicPressStoryline {
  var $_structure, $root_category;
  
  /**
   * Create a searchable structure from a node list.
   * @param array $structure The structure to process.
   * @return boolean True if the structure was valid.
   */
  function create_structure($structure) {
    $new_structure = array();
    $parent   = null;
    $all_leaves = array();
    $this->root_category = false;

    $adjacents_by_parent = array();

		if (is_string($structure)) {
			$structure = explode(',', $structure);
		}

    if (is_array($structure)) {
      $is_valid = true;
      foreach ($structure as $branch) {
        if (is_string($branch)) {
          $parts = explode('/', $branch);
          $valid = false;
          if (count($parts) > 1) {
            if ($parts[0] == '0') { $valid = true; }
          }
          if (!$valid) {
            $is_valid = false; break;
          } else {
            $data = array();
            $leaf = end($parts);
            $all_leaves[] = $leaf;

            if (count($parts) > 2) {
              $parent = $parts[count($parts) - 2];

              if (!isset($adjacents_by_parent[$parent])) {
                $adjacents_by_parent[$parent] = array();
              }
              $adjacents_by_parent[$parent][] = $leaf;

              $data['parent'] = $parent;
            } else {
              $this->root_category = $leaf;
            }

            $new_structure[$leaf] = $data;
          }
        } else {
          $is_valid = false; break;
        }
      }
      if ($is_valid) {
        for ($i = 0; $i < count($all_leaves); ++$i) {
          foreach (array('previous' => -1, 'next' => 1) as $type => $dir) {
            if (isset($all_leaves[$i + $dir])) {
              $new_structure[$all_leaves[$i]][$type] = $all_leaves[$i + $dir];
            }
          }
        }

        $this->_structure = $new_structure;
      }
    }
    return is_array($this->_structure);
  }

  function _get_field($field, $id) {
    if (isset($this->_structure)) {
      if (isset($this->_structure[$id])) {
        if (isset($this->_structure[$id][$field])) {
          return $this->_structure[$id][$field];
        }
      }
    }
    return false;
  }
  
  function parent($id)   { return $this->_get_field('parent', $id); }
  function previous($id) { return $this->_get_field('previous', $id); }
  function next($id)     { return $this->_get_field('next', $id); }
  function valid($id)    {
    if (isset($this->_structure[$id])) {
      return array_keys($this->_structure[$id]);
    }
    return false;
  }

  /**
   * Get the valid navigation directions for a particular post.
   */
  function get_valid_nav($post_id) {
    if (($category = $this->get_valid_post_category($post_id)) !== false) {
      return $this->valid($category);
    }
    return false;
  }

  /**
   * Get the valid comic category for this post.
   */
  function get_valid_post_category($post_id) {
    $result = false;
    
    foreach (wp_get_post_categories($post_id) as $category) {
      if ($this->valid($category)) {
        if ($result) { return false; }

        $result = $category;
      }
    }
    return $result;
  }

	function get_comic_categories() {
		return array_keys($this->_structure);
	}
	
	function get_simple_storyline() {
	  $simple_storyline = array('0' => array());
		foreach ($this->_structure as $category_id => $adjacents) {
			$parent = 0;
			if (isset($adjacents['parent'])) { $parent = $adjacents['parent']; }
			if (!isset($simple_storyline[$parent])) {
				$simple_storyline[$parent] = array();
			}
			$simple_storyline[$parent][$category_id] = true;
		}
		
		while (count($simple_storyline) > 0) {
			$merge_found = false;
			foreach ($simple_storyline as $parent => $children) {
				$has_no_descendents = true;
				foreach (array_keys($children) as $child) {
					if (is_numeric($child)) {
						if (isset($simple_storyline[$child])) {
							$has_no_descendents = false;
							break;
						}
					}
				}
				if ($has_no_descendents) {
					$merge_found = $parent; break;
				}
			}
			if ($merge_found !== false) {
				foreach ($simple_storyline as $parent => $children) {
					if (isset($children[$merge_found])) {
					  $simple_storyline[$parent][$merge_found] = $simple_storyline[$merge_found];
						unset($simple_storyline[$merge_found]);
						break;
					}
				}
			}
			if (!$merge_found) { break; }
		}
		return $simple_storyline;
	}
}

?>