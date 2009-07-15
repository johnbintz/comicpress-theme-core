<div class="post-comic-head"></div>
<div class="post-comic">
  <div class="comicarchiveframe" style="width:<?php echo $archive_comic_width ?>px;">
    <a href="<?php the_permalink() ?>"><img src="<?php the_comic_archive() ?>" alt="Click for full size." title="Click for full size" width="<?php echo $archive_comic_width ?>" /><br />
    <h3><?php the_title() ?></h3>
    <small><?php the_time('F jS, Y') ?></small></a>
  </div>
  <br class="clear-margins" />          
</div>
<div class="post-comic-foot"></div>