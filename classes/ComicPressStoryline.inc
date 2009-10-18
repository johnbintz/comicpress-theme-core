<?php

class ComicPressStoryline {
  var $_structure;
  
  function create_structure($structure) {
    $new_structure = array();
    $parent   = null;
    $previous = null;

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
      
        $this->_structure = $new_structure;
      }
    }
    return is_array($this->_structure);
  }
}

?>