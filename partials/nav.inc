<div class="nav">
  <?php if ($nav_comics['show_first']) { ?>
    <div class="nav-first">
      <a href="<?php echo get_permalink($nav_comics['first']->ID) ?>" 
         title="<?php _e('Go to the first comic', 'comicpress') ?>">&lsaquo;&lsaquo; <?php _e('First', 'comicpress') ?></a>
    </div>
  <?php } ?>
  <?php if ($nav_comics['show_previous']) { ?>
    <div class="nav-previous">
      <a href="<?php echo get_permalink($nav_comics['previous']->ID) ?>"
      title="<?php _e('Go to the previous comic', 'comicpress') ?>">&lsaquo; <?php _e('Previous', 'comicpress') ?></a>
    </div>
  <?php } ?>
  <?php if ($nav_comics['show_next']) { ?>
    <div class="nav-first">
      <a href="<?php echo get_permalink($nav_comics['next']->ID) ?>" 
         title="<?php _e('Go to the next comic', 'comicpress') ?>"><?php _e('Next', 'comicpress') ?> &rsaquo;</a>
    </div>
  <?php } ?>
  <?php if ($nav_comics['show_last']) { ?>
    <div class="nav-previous">
      <a href="<?php echo get_permalink($nav_comics['last']->ID) ?>"
      title="<?php _e('Go to the last comic', 'comicpress') ?>"><?php _e('Last', 'comicpress') ?> &rsaquo;&rsaquo;</a>
    </div>
  <?php } ?>
</div>
