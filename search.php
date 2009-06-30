<?php get_header() ?>

<div id="column" class="archive">

  <div class="post-page-head"></div>
  <div class="post-page">			
    <?php
      $tmp_search = new WP_Query('s=' . wp_specialchars($_GET['s']) . '&show_posts=-1&posts_per_page=-1');
      $count = $tmp_search->post_count;
    ?>
    <h2 class="pagetitle">Search for &lsquo;<?php the_search_query() ?>&rsquo;</h2>
    Found <?php echo $count; ?> result<?php if ($count !== 1) { echo "s"; } ?>.
  </div>
  <div class="post-page-foot"></div>

  <?php if (have_posts()) : ?>
    
    <?php $posts = query_posts($query_string.'&order=asc');
    while (have_posts()) : the_post() ?>
      
        <?php global $archive_comic_width; if (in_comic_category()) { ?>

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

        <?php } else { ?>

          <div class="post-head"></div>
          <div class="post">
            <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title() ?>"><?php the_title() ?></a></h3>
            <div class="postdate"><?php the_time('F jS, Y') ?></div>
            <?php the_excerpt() ?>
            <br class="clear-margins" />
          </div>
          <div class="post-foot"></div>
          
        <?php } ?>
      
    <?php endwhile; ?>
    
    <div class="pagenav">
      <div class="pagenav-right"><?php next_posts_link('Next Page &rsaquo;') ?></div>
      <div class="pagenav-left"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
      <div class="clear"></div>
    </div>
    
  <?php else : ?>

    <div class="post-page-head"></div>
    <div class="post-page">
      <h3>No entries found.</h3>
      <p>Try another search?</p>
      <p><?php include (TEMPLATEPATH . '/searchform.php') ?></p>
      <br class="clear-margins" />
    </div>
    <div class="post-page-foot"></div>

  <?php endif; ?>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>