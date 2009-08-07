<div class="post-comic-head"></div>
<div class="post-comic">
  <?php include_partial('nav') ?>
  <div class="comicdate"><?php the_date('F jS, Y') ?></div>
  <br class="clear" />
  <h2>
    <a href="<?php get_permalink($last_comic_post->ID) ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'comicpress'), get_the_title()) ?>"><?php the_title() ?></a>
  </h2>
  <div class="entry"><?php the_content('&darr; Read the rest of this entry...') ?></div>
  <?php the_transcript('styled') ?>
  <div class="tags"><?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?></div>
  <div class="comment-link">
    <?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
  </div>
  <br class="clear" />
</div>
<div class="post-comic-foot"></div>
