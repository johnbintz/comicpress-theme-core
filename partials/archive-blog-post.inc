<div class="post-head"></div>
<div class="post archive">
  <h3 id="post-<?php the_ID() ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute() ?>"><?php the_title() ?></a></h3>
  <div class="postdate"><?php the_time('F jS, Y') ?></div>
  <div class="entry"><?php the_content() ?></div> 
  <div class="tags">
    <?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?>
  </div>
  <div class="comment-link">
    <?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
  </div>
  <br class="clear-margins" />
</div>
<div class="post-foot"></div>