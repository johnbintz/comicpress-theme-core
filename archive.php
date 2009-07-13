<?php get_header() ?>

<div id="content" class="archive">
	<?php if (have_posts()) {
    include(dirname(__FILE__) . '/partials/archive-header.inc');
    
    // why can't wordpress allow for post date asc sorting out of the box...
    
    $posts = query_posts($query_string.'&order=asc');
		while (have_posts()) {
      the_post();
      
      if (in_comic_category()) {
        include(dirname(__FILE__) . '/partials/archive-comic-post.inc');
      } else {
        include(dirname(__FILE__) . '/partials/archive-blog-post.inc');
      }
    } ?>

		<div class="pagenav">
			<div class="pagenav-right"><?php next_posts_link('Next Page &rsaquo;') ?></div>
			<div class="pagenav-left"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
			<div class="clear"></div>
		</div>

	<?php } else { ?>

		<div class="post-head"></div>
		<div class="post">
			<h3>No entries found.</h3>
			<p>Try another search?</p>
			<p><?php include (TEMPLATEPATH . '/searchform.php') ?></p>
			<br class="clear-margins" />
		</div>
		<div class="post-foot"></div>

	<?php } ?>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>