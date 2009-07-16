<?php get_header() ?>

<div id="content" class="archive">
  <?php 
    include_partial('search-results-count');

    if (have_posts()) {
      query_posts($query_string . '&order=asc');
      
      while (have_posts()) {
        the_post();
    
        if (in_comic_category()) {
          include_partial('search-comic-post', 'archive-comic-post');
        } else {
          include_partial('search-blog-post', 'archive-blog-post');
        }
      }
      
      include_partial('search-page-nav', 'archive-page-nav');
    } else {
      include_partial('search-not-found');
    }
  ?>
</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>