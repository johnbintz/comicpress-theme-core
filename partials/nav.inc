<div class="nav">
  <?php foreach (apply_filters('comicpress_nav_fields', array()) as $field => $link_text) { ?>
    <?php if ($nav_comics['show_' . $field]) { ?>
      <div class="nav-<?php echo $field ?>">
        <a href="<?php echo (is_object($nav_comics[$field])) ? get_permalink($nav_comics[$field]->ID) : $nav_comics[$field] ?>" 
           title="<?php sprintf(__('Go to the %s comic', 'comicpress'), $field) ?>"><?php echo apply_filters('comicpress_nav', $link_text, $field) ?></a>
      </div>
    <?php } ?>
  <?php } ?>
</div>