<?php get_header() ?>

<div id="content" class="archive">
  <?php 
    include(get_template_directory() . '/partials/search-results-count.inc');

    if (have_posts()) {
      query_posts($query_string . '&order=asc');
      
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
      include(get_template_directory() . '/partials/search-not-found.inc');
    }
  ?>
</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>