<?php
  global $comicpress, $query_string;

  comicpress_init();

  ob_start();

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

  $content = ob_get_clean();
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>