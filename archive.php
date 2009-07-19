<?php get_header() ?>

<div id="content" class="archive">
	<?php if (have_posts()) {
    include_partial('archive-header');
    
    // why can't wordpress allow for post date asc sorting out of the box...
    
    query_posts($query_string . '&order=asc');
    while (have_posts()) {
      the_post();
      
      if (in_comic_category()) {
        include_partial('archive-comic-post');
      } else {
        include_partial('archive-blog-post');
      }
    }

    include_partial('archive-page-nav');
	} else {
    include_partial('archive-not-found');	
  } ?>

</div>

<?php get_sidebar() ?>

<?php get_footer() ?>