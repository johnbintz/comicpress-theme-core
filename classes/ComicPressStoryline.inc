<?php

class ComicPressStoryline {
  var $_structure;
  
  /**
   * Create a searchable structure from a node list.
   * @param array $structure The structure to process.
   * @return boolean True if the structure was valid.
   */
  function create_structure($structure) {
    $new_structure = array();
    $parent   = null;
    $all_leaves = array();

    $adjacents_by_parent = array();

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
            }

            $new_structure[$leaf] = $data;
          }
        } else {
          $is_valid = false; break;
        }
      }
      if ($is_valid) {
        foreach ($adjacents_by_parent as $parent => $adjacents) {
          for ($i = 0; $i < count($adjacents); ++$i) {
            foreach (array('previous' => -1, 'next' => 1) as $type => $dir) {
              if (isset($adjacents[$i + $dir])) {
                $new_structure[$adjacents[$i]][$type] = $adjacents[$i + $dir];
              }
            }
          }
        }
        
        for ($i = 0; $i < count($all_leaves); ++$i) {
          foreach (array('prior' => -1, 'upcoming' => 1) as $type => $dir) {
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
  function upcoming($id) { return $this->_get_field('upcoming', $id); }
  function prior($id)    { return $this->_get_field('prior', $id); }
  function valid($id)    {
    if (isset($this->_structure[$id])) {
      return array_keys($this->_structure[$id]);
    }
    return false;
  }

  function get_valid_storyline_nav($post_id) {
    $data = false;

    foreach (wp_get_post_categories($post_id) as $category) {
      if ($result = $this->valid($category)) {
        if ($data) { return false; }

        $data = $result;
      }
    }
    return $data;
  }  
}

?>