<?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
  <div class="pagenav">
    <div class="pagenav-right"><?php next_posts_link('Next Page &rsaquo;') ?></div>
    <div class="pagenav-left"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
    <div class="clear"></div>
  </div>
<?php } ?>
