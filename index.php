<?php
  global $comicpress;

  comicpress_init();

  $nav_comics = $comicpress->get_nav_comics();
  $t = $post;
  $post = $nav_comics['last'];  

  ob_start();
  
  if (!is_paged()) { include_partial('index-display-comic'); }

  $comic = ob_get_clean();

  ob_start();

  if (!is_paged() && ($comicpress->comicpress_options['comic_space'] == "comic_only")) { include_partial('index-comic-post'); }

  include_partial('index-blog-header');

  foreach ($comicpress->get_index_blog_posts() as $post) {
    include_partial('index-blog-post');
  }

  $content = ob_get_clean();
  $post = $t;
  
  include(get_template_directory() . '/layouts/classic.php');
?>