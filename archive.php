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
	} else {
    include(get_template_directory() . '/partials/archive-not-found.inc');	
  } ?>

</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>