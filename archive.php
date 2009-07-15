<?php get_header() ?>

<div id="content" class="archive">
	<?php if (have_posts()) {
    include(get_template_directory() . '/partials/archive-header.inc');
    
    // why can't wordpress allow for post date asc sorting out of the box...
    
    $posts = query_posts($query_string . '&order=asc');
		while (have_posts()) {
      the_post();
      
      if (in_comic_category()) {
        include(get_template_directory() . '/partials/archive-comic-post.inc');
      } else {
        include(get_template_directory() . '/partials/archive-blog-post.inc');
      }
    }

    include(get_template_directory() . '/partials/archive-page-nav.inc');

	<?php } else { ?>

		<div class="post-head"></div>
		<div class="post">
			<h3>No entries found.</h3>
			<p>Try another search?</p>
			<p><?php include (get_template_directory() . '/searchform.php') ?></p>
			<br class="clear-margins" />
		</div>
		<div class="post-foot"></div>

	<?php } ?>

</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>