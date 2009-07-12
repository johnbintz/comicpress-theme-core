<div class="post-comic-head"></div>
<div class="post-comic" id="post-<?php the_ID() ?>">
  <div class="comicdate">
    <?php include('nav.inc') ?>
    <?php the_time('F jS, Y') ?>
  </div>
  <?php if (get_option('comicpress-enable-storyline-support') == 1) { ?>
    <ul class="storyline-cats"><li class="storyline-root"><?php the_category(' &raquo; </li><li>', 'multiple') ?></li></ul>
  <?php } ?>
  <h2><?php the_title() ?></h2>
  <div class="entry">
    <?php the_content() ?>
    <?php the_transcript('styled') ?>     
    <div class="tags">
      <?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?> 
    </div>
    <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
  </div>
  <br class="clear-margins" />
</div>
<div class="post-comic-foot"></div>
