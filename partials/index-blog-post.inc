<div class="post-head"></div>
<div class="post" id="post-<?php the_ID() ?>">
  <h3>
    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'comicpress'), get_the_title()) ?>"><?php the_title() ?></a>
  </h3>
  <div class="postdate">
    <?php the_time('F jS, Y') ?>
  </div>
  <div class="entry">
    <?php the_content(__('&darr; Read the rest of this entry...', 'comicpress')) ?>
  </div> 
  <div class="tags">
    <?php the_tags(__('&#9492; Tags: ', 'comicpress'), ', ', ''); edit_post_link(__('Edit Post', 'comicpress'), ' [ ', ' ] ') ?>
  </div>
  <div class="comment-link">
    <?php if ('open' == $post->comment_status) { comments_popup_link(__('&ldquo;Comment&rdquo;', 'comicpress'), __('&ldquo;1 Comment&rdquo;', 'comicpress'), __('&ldquo;% Comments&rdquo;', 'comicpress')); } ?>
  </div>
  <div class="clear-margins"><br /></div>
</div>
<div class="post-foot"></div>