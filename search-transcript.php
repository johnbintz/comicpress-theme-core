<?php
  global $comicpress, $post, $query_string;

  comicpress_init();

  ob_start();
  
  include_partial('search-transcript-results-count');
  
  if (have_posts()) {
    query_posts($query_string.'&order=asc');
   
    while (have_posts()) {
      the_post();

      if (in_comic_category()) {
        include_partial('search-transcript-comic-post', 'search-comic-post', 'archive-comic-post');        
      } else {
        include_partial('search-transcript-blog-post', 'search-blog-post', 'archive-blog-post');                
      }
    }
    
    include_partial('search-transcript-page-nav', 'search-page-nav', 'archive-page-nav');

  } else {
    include_partial('search-transcript-not-found');    
  }

  $content = ob_get_clean();
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>
