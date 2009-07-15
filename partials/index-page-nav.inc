<?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
  <div class="pagenav">
    <div class="pagenav-right"><?php previous_posts_link(__('Newer Entries &uarr;', 'comicpress')) ?></div>
    <div class="pagenav-left"><?php next_posts_link(__('&darr; Previous Entries', 'comicpress')) ?></div>
    <div class="clear"></div>
  </div>  
<?php } ?>