<div class="post-head"></div>
<div class="post" id="post-<?php the_ID() ?>">
  <div class="nav-blog">
    <div class="nav-blog-previous"><?php previous_post_link('%link','&lsaquo; Previous', TRUE) ?></div>
    <div class="nav-blog-next"><?php next_post_link('%link','Next &rsaquo;', TRUE) ?></div>
  </div>
  <div class="clear"></div>
  <h2><?php the_title() ?></h2>
  <div class="postdate"><?php the_time('F jS, Y') ?></div>
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
<div class="post-foot"></div>
<?php comments_template(); ?>
