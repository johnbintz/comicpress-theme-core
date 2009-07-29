<?php
  $nav_fields = array(
    'first'    => '&lsaquo;&lsaquo; ' . __('First', 'comicpress'),
    'previous' => '&lsaquo; ' . __('Previous', 'comicpress'),
    'next'     => __('Next', 'comicpress') . ' &rsaquo;',
    'last'     => __('Last', 'comicpress') . ' &rsaquo;&rsaquo;'
  );

  if ($comicpress->needs_storyline_nav()) {
    $nav_fields = array_merge(
      array('prior'    => '&lsaquo;&lsaquo; ' . __('Prior Storyline', 'comicpress')),
      $nav_fields,
      array('upcoming' => __('Upcoming Storyline', 'comicpress')  . ' &rsaquo;&rsaquo;')
    );
    
    foreach ($comicpress->get_sorted_post_categories() as $category_id) {
      $prev_next_categories = $comicpress->get_previous_next_categories($category_id);
      
      foreach ($prev_next_categories as $master_id => $cat_list) {
        foreach ($cat_list as $which => $id) {
          switch ($which) {
            case "previous":
              $terminal_post = $comicpress->get_last_comic($id);
              $which_field = "prior";
              break;
            case "next":
              $terminal_post = $comicpress->get_first_comic($id);
              $which_field = "upcoming";
              break;
          }
          
          if (is_object($terminal_post)) {
            $nav_comics[$which_field] = $terminal_post;
            $nav_comics["show_${which_field}"] =true;
          }
          
          if (count($terminal_post) == 2) { break; }
        }
      }
    }
  }

  ?>
  <div class="nav">
    <?php foreach ($nav_fields as $field => $link_text) { ?>
      <?php if ($nav_comics['show_' . $field]) { ?>
        <div class="nav-<?php echo $field ?>">
          <a href="<?php echo (is_object($nav_comics[$field])) ? get_permalink($nav_comics[$field]->ID) : $nav_comics[$field] ?>" 
             title="<?php sprintf(__('Go to the %s comic', 'comicpress'), $field) ?>"><?php echo $link_text ?></a>
        </div>
      <?php } ?>
    <?php } ?>
  </div>