<?php get_header() ?>

<div id="content" class="archive">

  <div class="post-page-head"></div>
  <div class="post-page">			
    <?php
      $tmp_search = new WP_Query('s=' . wp_specialchars($_GET['s']) . '&show_posts=-1&posts_per_page=-1');
      $count = $tmp_search->post_count;
    ?>
    <h2 class="pagetitle">Transcript search for &lsquo;<?php the_search_query() ?>&rsquo;</h2>
    Found <?php echo $count; ?> result<?php if ($count !== 1) { echo "s"; } ?>.
  </div>
  <div class="post-page-foot"></div>

  <?php if (have_posts()) : ?>

    <?php $posts = query_posts($query_string.'&order=asc');
    while (have_posts()) : the_post() ?>

        <?php if (in_comic_category()) { ?>

          <div class="post-comic-head"></div>
          <div class="post-comic">
				<div class="comicdate">
					<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
				</div>
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				By <?php the_author(); ?> on <?php the_time('F jS, Y') ?> <?php edit_post_link('[edit this]'); ?> <br />
				Posted in: <?php the_category(','); ?>		
            <div class="comicarchiveframe" style="width:<?php echo $archive_comic_width ?>px;">
				<a href="<?php the_permalink() ?>"><img src="<?php the_comic_archive() ?>" alt="<?php the_title() ?>" title="<?php the_transcript() ?>" width="<?php echo $archive_comic_width ?>" /><br />
            </div>
            <br class="clear-margins" />
          </div>
          <div class="post-comic-foot"></div>

        <?php } else { ?>

          <div class="post-head"></div>
          <div class="post">
				<div class="comicdate">
					<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
				</div>
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				By <?php the_author(); ?> on <?php the_time('F jS, Y') ?> <?php edit_post_link('[edit this]'); ?> <br />
				Posted in: <?php the_category(','); ?>		
            <?php the_excerpt() ?>
            <br class="clear-margins" />
          </div>
          <div class="post-foot"></div>
          
        <?php } ?>

    <?php endwhile; ?>

    <?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
      <div class="pagenav">
        <div class="pagenav-right"><?php next_posts_link('Next Page &rsaquo;') ?></div>
        <div class="pagenav-left"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
        <div class="clear"></div>
      </div>
    <?php } ?>

  <?php else : ?>

    <div class="post-page-head"></div>
    <div class="post-page">
      <h3>No transcripts found.</h3>
      <p>Try another search?</p>
      <p><?php include (TEMPLATEPATH . '/searchform-transcript.php') ?></p>
      <br class="clear-margins" />
    </div>
    <div class="post-page-foot"></div>

  <?php endif; ?>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>