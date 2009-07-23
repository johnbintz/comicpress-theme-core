<?php
  global $comicpres, $post;

  comicpress_init();
  
  ob_start();
  
  if (have_posts()) {
    while (have_posts()) {
      the_post();
      include_partial('image-post');
    }
  } else {
    include_partial('image-not-found'); 
  }

  $content = ob_get_clean();
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>