<?php

require_once('ComicPress.inc');
require_once('ComicPressDBInterface.inc');

class ComicPressStoryline {
  var $_structure, $root_category;

	function read_from_options() {
		$this->create_structure($this->get_flattened_storyline());
	}
	
	function get_flattened_storyline() {
		$comicpress = &ComicPress::get_instance();
	  return $comicpress->comicpress_options['storyline_order'];
	}

	function set_flattened_storyline($storyline) {
		$comicpress = &ComicPress::get_instance();
		$comicpress->comicpress_options['storyline_order'] = $storyline;
		$comicpress->save();
	}

	function set_order_via_flattened_storyline($order) {
		$nodes = explode(',', $order);
		$original_nodes = explode(',', $this->get_flattened_storyline());
		
		$missing_good_nodes = array_diff($original_nodes, $nodes);
		$any_bad_nodes      = array_diff($nodes, $original_nodes);
		
		if (empty($missing_good_nodes) && empty($any_bad_nodes)) {
			$this->set_flattened_storyline($order);
			return true;
		} else {
			return false;
		}
	}

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
		
		return $this->_merge_simple_storyline($simple_storyline);
	}

	function get_category_simple_structure($parent) {
		$structure = array();
		foreach (get_all_category_ids() as $category_id) {
			$category = get_category($category_id);
			if (!isset($structure[$category->parent])) {
				$structure[$category->parent] = array();
			}
			$structure[$category->parent][$category_id] = true;
		}
		$structure = $this->_merge_simple_storyline($structure);
		if (isset($structure[0])) {
			foreach ($structure[0] as $key => $children) {
				if ($key != $parent) { unset($structure[0][$key]); }
			}
		}
		return $structure;
	}
	
	function get_category_flattened($parent) {
		return $this->flatten_simple_storyline($this->get_category_simple_structure($parent));
	}

	function _merge_simple_storyline($simple_storyline) {
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

	/**
	 * Integrates a bunch of other things.
	 */
	function normalize($flattened_storyline = null, $set = true) {
		$comicpress = ComicPress::get_instance();
		if (is_null($flattened_storyline)) {
			$flattened_storyline = $this->get_flattened_storyline();
		}
		$all_categories_flattened = $this->get_category_flattened($comicpress->comicpress_options['comic_category_id']);
		
		$result = $this->normalize_flattened_storyline($flattened_storyline, $all_categories_flattened);
		if ($set) {
			$this->set_flattened_storyline($result);
		}
		return $result;
	}

	function _length_sort($parts) {
		$new = array();
		foreach ($parts as $part) {
		  $p = explode('/', $part);
			if (!isset($new[count($p)])) {
				$new[count($p)] = array();
			}
			$new[count($p)][] = $part;
		}
		ksort($new);
		$output = array();
		foreach (array_values($new) as $values) {
			$output = array_merge($output, $values);
		}
		return $output;
	}

	function normalize_flattened_storyline($storyline, $comic_categories) {
		$storyline_nodes = explode(",", $storyline);
		$category_nodes  = explode(",", $comic_categories);
		
		$missing_from_storyline = array_diff($category_nodes, $storyline_nodes);
		$extra_in_storyline     = array_diff($storyline_nodes, $category_nodes);
		
		if (!empty($missing_from_storyline)) {
			$missing_from_storyline = $this->_length_sort($missing_from_storyline);
			foreach ($missing_from_storyline as $node) {
				$parent_pattern = implode('/', array_slice(explode('/', $node), 0, -1));
				$last = null;
				for ($i = 0, $il = count($storyline_nodes); $i < $il; ++$i) {
					if (strpos($storyline_nodes[$i], $parent_pattern) === 0) {
						$last = $i;
					}
				}
				if (!is_null($last)) {
					array_splice($storyline_nodes, $last + 1, 0, array($node));
				} else {
					$storyline_nodes[] = $node;
				}
			}
		}
		
		if (!empty($extra_in_storyline)) {
			$new = array();
			foreach ($storyline_nodes as $node) {
				if (!in_array($node, $extra_in_storyline)) {
					$new[] = $node;
				}
			}
			$storyline_nodes = $new;
		}
		
		return implode(',', $storyline_nodes);
	}
	
	function flatten_simple_storyline($storyline) {
		return implode(',', $this->_follow_simple_storyline($storyline));
	}
	
	function _follow_simple_storyline($storyline, $parent = null) {
		$output = array();
		foreach ($storyline as $key => $children) {
			if (is_null($parent)) {
				$new_parent = $key;
			} else {
				$new_parent = $parent . '/' . $key;
				$output[] = $new_parent;
			}
			if (is_array($children)) {
				$output = array_merge($output, $this->_follow_simple_storyline($children, $new_parent));
			}
		}
		return $output;
	}
}

?>